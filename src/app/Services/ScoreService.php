<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\ScoreHistorico;

class ScoreService
{
    private const PONTOS_EM_DIA          =   5;
    private const PONTOS_ATRASO_CURTO    = -10; // 1–7 dias de atraso
    private const PONTOS_ATRASO_LONGO    = -25; // 8+ dias de atraso ou inadimplência

    public function aplicarPagamento(Parcela $parcela): void
    {
        $emDia = $parcela->data_pagamento->lte($parcela->vencimento);

        if ($emDia) {
            $pontos = self::PONTOS_EM_DIA;
        } else {
            $diasAtraso = (int) $parcela->vencimento->startOfDay()->diffInDays($parcela->data_pagamento->startOfDay());
            $pontos = $diasAtraso <= 7 ? self::PONTOS_ATRASO_CURTO : self::PONTOS_ATRASO_LONGO;
        }

        $this->aplicar($parcela, $pontos);
    }

    public function aplicarAtraso(Parcela $parcela): void
    {
        $jaAplicado = ScoreHistorico::where('parcela_id', $parcela->id)
            ->where('pontos_aplicados', self::PONTOS_ATRASO_LONGO)
            ->exists();

        if ($jaAplicado) {
            return;
        }

        $this->aplicar($parcela, self::PONTOS_ATRASO_LONGO);
    }

    private function aplicar(Parcela $parcela, int $pontos): void
    {
        $cliente = Cliente::lockForUpdate()->find($parcela->cobranca->cliente_id);

        $novoScore = max(0, min(100, $cliente->score_atual + $pontos));
        $categoria = $this->calcularCategoria($novoScore);

        $cliente->update([
            'score_atual'     => $novoScore,
            'score_categoria' => $categoria,
        ]);

        ScoreHistorico::create([
            'cliente_id'       => $cliente->id,
            'parcela_id'       => $parcela->id,
            'pontos_aplicados' => $pontos,
            'score_resultante' => $novoScore,
        ]);
    }

    private function calcularCategoria(int $score): string
    {
        return match(true) {
            $score >= 80 => 'bom_pagador',
            $score >= 50 => 'atencao',
            default      => 'risco',
        };
    }
}
