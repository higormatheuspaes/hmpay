<?php

namespace App\Jobs;

use App\Models\Parcela;
use App\Services\MensagemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EnviarLembretes implements ShouldQueue
{
    use Queueable;

    public function handle(MensagemService $service): void
    {
        // Lembrete no dia do vencimento
        Parcela::with('cobranca.empresa', 'cobranca.cliente')
            ->where('status', 'pendente')
            ->whereDate('vencimento', today())
            ->each(fn($p) => $service->enviarLembreteDia($p));

        // Lembrete X dias antes (por empresa, conforme configuração)
        Parcela::with('cobranca.empresa', 'cobranca.cliente')
            ->where('status', 'pendente')
            ->whereHas('cobranca.empresa', fn($q) => $q->where('notif_lembrete_antes_ativo', true))
            ->get()
            ->filter(function (Parcela $p) {
                $dias = $p->cobranca->empresa->notif_lembrete_antes_dias ?? 3;
                return $p->vencimento->isToday() === false
                    && $p->vencimento->diffInDays(today()) === $dias
                    && $p->vencimento->isFuture();
            })
            ->each(fn($p) => $service->enviarLembreteAntes($p));
    }
}
