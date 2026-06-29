<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\ScoreHistorico;
use App\Services\ScoreService;
use Illuminate\Console\Command;

class RecalcularDados extends Command
{
    protected $signature   = 'hmpay:recalcular';
    protected $description = 'Normaliza status das parcelas e recalcula scores do zero';

    public function handle(): void
    {
        $this->info('Normalizando status das parcelas...');
        $this->normalizarStatus();

        $this->info('Zerando histórico de scores...');
        ScoreHistorico::truncate();

        $this->info('Recalculando scores...');
        $this->recalcularScores();

        $this->info('Concluído.');
    }

    private function normalizarStatus(): void
    {
        $hoje = now()->startOfDay();

        // pendente com vencimento passado → atrasado
        $atrasados = Parcela::where('status', 'pendente')->whereDate('vencimento', '<', $hoje)->count();
        Parcela::where('status', 'pendente')->whereDate('vencimento', '<', $hoje)->update(['status' => 'atrasado']);
        $this->line("  pendente vencido → atrasado: {$atrasados} parcelas");

        $this->line("  pago/pendente: já nos valores corretos do enum");
    }

    private function recalcularScores(): void
    {
        $service = new ScoreService;

        Cliente::all()->each(function (Cliente $cliente) use ($service) {
            $cliente->update(['score_atual' => 100, 'score_categoria' => 'bom_pagador']);

            $parcelas = Parcela::whereHas('cobranca', fn($q) => $q->where('cliente_id', $cliente->id))
                ->where('status', 'pago')
                ->whereNotNull('data_pagamento')
                ->orderBy('data_pagamento')
                ->with('cobranca')
                ->get();

            foreach ($parcelas as $parcela) {
                $service->aplicarPagamento($parcela);
            }

            $cliente->refresh();
            $this->line("  {$cliente->nome}: score={$cliente->score_atual} ({$cliente->score_categoria})");
        });
    }
}
