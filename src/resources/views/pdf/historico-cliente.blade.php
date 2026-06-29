@extends('pdf.layout')

@section('content')
<h2 style="font-size:14px; font-weight:700; margin-bottom:4px;">Histórico por Cliente</h2>
<p style="font-size:10px; color:#6b7280; margin-bottom:12px;">
    {{ $cliente->nome }} — Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
</p>

@php
    $totalPago    = $parcelas->where('status', 'pago')->sum('valor');
    $totalAberto  = $parcelas->where('status', 'pendente')->sum('valor');
    $totalAtrasado = $parcelas->filter(fn($p) => $p->status === 'pendente' && $p->vencimento->isPast())->sum('valor');
@endphp

<div class="summary">
    <div class="summary-card">
        <div class="label">Total pago</div>
        <div class="value" style="color:#059669;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="label">Em aberto</div>
        <div class="value">R$ {{ number_format($totalAberto, 2, ',', '.') }}</div>
    </div>
    @if($totalAtrasado > 0)
    <div class="summary-card">
        <div class="label">Atrasado</div>
        <div class="value" style="color:#dc2626;">R$ {{ number_format($totalAtrasado, 2, ',', '.') }}</div>
    </div>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>Cobrança</th>
            <th>#</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Data Pagamento</th>
            <th style="text-align:right;">Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($parcelas as $p)
        @php
            $atrasada = $p->status === 'pendente' && $p->vencimento->isPast();
            $badgeClass = match(true) {
                $p->status === 'pago'      => 'badge-green',
                $p->status === 'cancelado' => 'badge-gray',
                $atrasada                  => 'badge-red',
                default                    => 'badge-yellow',
            };
            $badgeLabel = match(true) {
                $p->status === 'pago'      => 'Pago',
                $p->status === 'cancelado' => 'Cancelado',
                $atrasada                  => 'Atrasado',
                default                    => 'Pendente',
            };
        @endphp
        <tr>
            <td>{{ $p->cobranca->descricao }}</td>
            <td>{{ $p->numero }}</td>
            <td>{{ $p->vencimento->format('d/m/Y') }}</td>
            <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
            <td>{{ $p->data_pagamento?->format('d/m/Y') ?? '—' }}</td>
            <td style="text-align:right; font-weight:600;">{{ number_format($p->valor, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; padding:20px; color:#9ca3af;">Nenhuma parcela no período</td></tr>
        @endforelse
    </tbody>
    @if($parcelas->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="5">Total</td>
            <td style="text-align:right;">{{ number_format($parcelas->sum('valor'), 2, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
