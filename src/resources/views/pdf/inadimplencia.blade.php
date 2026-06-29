@extends('pdf.layout')

@section('content')
<h2 style="font-size:14px; font-weight:700; margin-bottom:12px;">Relatório de Inadimplência</h2>

@php
    $totalValor = $parcelas->sum('valor');
    $totalClientes = $parcelas->pluck('cobranca.cliente.nome')->unique()->count();
@endphp

<div class="summary">
    <div class="summary-card">
        <div class="label">Parcelas atrasadas</div>
        <div class="value">{{ $parcelas->count() }}</div>
    </div>
    <div class="summary-card">
        <div class="label">Clientes inadimplentes</div>
        <div class="value">{{ $totalClientes }}</div>
    </div>
    <div class="summary-card">
        <div class="label">Valor total em aberto</div>
        <div class="value" style="color:#dc2626;">R$ {{ number_format($totalValor, 2, ',', '.') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Cobrança</th>
            <th>#</th>
            <th>Vencimento</th>
            <th>Dias em atraso</th>
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
            <td><span class="badge badge-red">{{ (int) now()->startOfDay()->diffInDays($p->vencimento->startOfDay()) }}d</span></td>
            <td style="text-align:right; font-weight:600; color:#dc2626;">{{ number_format($p->valor, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; padding:20px; color:#9ca3af;">Nenhuma parcela em atraso</td></tr>
        @endforelse
    </tbody>
    @if($parcelas->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="5">Total</td>
            <td style="text-align:right; color:#dc2626;">{{ number_format($totalValor, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
