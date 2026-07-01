<?php

namespace App\Jobs;

use App\Models\Parcela;
use App\Services\MensagemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EnviarAvisosAtraso implements ShouldQueue
{
    use Queueable;

    public function handle(MensagemService $service): void
    {
        Parcela::with('cobranca.empresa', 'cobranca.cliente')
            ->where(fn($q) => $q
                ->where('status', 'atrasado')
                ->orWhere(fn($q) => $q
                    ->where('status', 'pendente')
                    ->whereDate('vencimento', '<', today())
                )
            )
            ->whereHas('cobranca.empresa', fn($q) => $q->where('notif_aviso_atraso_ativo', true))
            ->each(fn($p) => $service->enviarAvisoAtraso($p));
    }
}
