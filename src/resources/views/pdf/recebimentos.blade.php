@extends('pdf.layout')

@section('content')
<h2 style="font-size:14px; font-weight:700; margin-bottom:4px;">Extrato de Recebimentos</h2>
<p style="font-size:10px; color:#6b7280; margin-bottom:12px;">
    Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
</p>

@php $totalValor = $parcelas->sum('valor'); @endphp

<div class="summary">
    <div class="summary-card">
        <div class="label">Pagamentos recebidos</div>
        <div class="value">{{ $parcelas->count() }}</div>
    </div>
    <div class="summary-card">
        <div class="label">Total recebido</div>
        <div class="value" style="color:#059669;">R$ {{ number_format($totalValor, 2, ',', '.') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Cobrança</th>
            <th>#</th>
            <th>Vencimento</th>
            <th>Data Pagamento</th>
            <th style="text-align:right;">Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($parcelas as $p)
        <tr>
            <td>{{ $p->cobranca->cliente->nome }}</td>
            <td>{{ $p->cobranca->descricao }}</td>
            <td>{{ $p->numero }}</td>
            <td>{{ $p->vencimento->format('d/m/Y') }}</td>
            <td>{{ $p->data_pagamento->format('d/m/Y') }}</td>
            <td style="text-align:right; font-weight:600; color:#059669;">{{ number_format($p->valor, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; padding:20px; color:#9ca3af;">Nenhum recebimento no período</td></tr>
        @endforelse
    </tbody>
    @if($parcelas->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="5">Total</td>
            <td style="text-align:right; color:#059669;">{{ number_format($totalValor, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
