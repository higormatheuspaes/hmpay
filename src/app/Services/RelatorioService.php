<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Parcela;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RelatorioService
{
    private function empresaId(): int
    {
        return Auth::user()->empresa_id;
    }

    public function inadimplencia(): Collection
    {
        return Parcela::with(['cobranca.cliente'])
            ->whereHas('cobranca', fn($q) => $q->where('empresa_id', $this->empresaId()))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '<', now())
            ->orderBy('vencimento')
            ->get();
    }

    public function recebimentos(string $dataInicio, string $dataFim, ?int $clienteId = null): Collection
    {
        return Parcela::with(['cobranca.cliente'])
            ->whereHas('cobranca', fn($q) => $q->where('empresa_id', $this->empresaId()))
            ->where('status', 'pago')
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->when($clienteId, fn($q) => $q->whereHas('cobranca', fn($q) =>
                $q->where('cliente_id', $clienteId)
            ))
            ->orderBy('data_pagamento')
            ->get();
    }

    public function fluxoCaixa(int $dias = 30): Collection
    {
        return Parcela::with(['cobranca.cliente'])
            ->whereHas('cobranca', fn($q) => $q->where('empresa_id', $this->empresaId()))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '>=', now())
            ->whereDate('vencimento', '<=', now()->addDays($dias))
            ->orderBy('vencimento')
            ->get();
    }

    public function historicoCliente(int $clienteId, string $dataInicio, string $dataFim): Collection
    {
        return Parcela::with(['cobranca'])
            ->whereHas('cobranca', fn($q) => $q
                ->where('empresa_id', $this->empresaId())
                ->where('cliente_id', $clienteId)
            )
            ->whereBetween('vencimento', [$dataInicio, $dataFim])
            ->orderBy('vencimento')
            ->get();
    }

    public function clientes(): Collection
    {
        return Cliente::where('empresa_id', $this->empresaId())
            ->orderBy('nome')
            ->get(['id', 'nome']);
    }
}
