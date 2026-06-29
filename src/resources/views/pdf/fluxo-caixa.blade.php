@extends('pdf.layout')

@section('content')
<h2 style="font-size:14px; font-weight:700; margin-bottom:4px;">Fluxo de Caixa Previsto</h2>
<p style="font-size:10px; color:#6b7280; margin-bottom:12px;">
    Próximos {{ $dias }} dias — de {{ now()->format('d/m/Y') }} a {{ now()->addDays($dias)->format('d/m/Y') }}
</p>

@php
    $totalValor = $parcelas->sum('valor');
    $porSemana  = $parcelas->groupBy(fn($p) => $p->vencimento->startOfWeek()->format('d/m/Y'));
@endphp

<div class="summary">
    <div class="summary-card">
        <div class="label">Parcelas a vencer</div>
        <div class="value">{{ $parcelas->count() }}</div>
    </div>
    <div class="summary-card">
        <div class="label">Total previsto</div>
        <div class="value" style="color:#2563eb;">R$ {{ number_format($totalValor, 2, ',', '.') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Cobrança</th>
            <th>#</th>
            <th>Vencimento</th>
            <th>Dias restantes</th>
            <th style="text-align:right;">Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($parcelas as $p)
        @php $diasRestantes = (int) now()->startOfDay()->diffInDays($p->vencimento->startOfDay()); @endphp
        <tr>
            <td>{{ $p->cobranca->cliente->nome }}</td>
            <td>{{ $p->cobranca->descricao }}</td>
            <td>{{ $p->numero }}</td>
            <td>{{ $p->vencimento->format('d/m/Y') }}</td>
            <td>
                <span class="badge {{ $diasRestantes <= 3 ? 'badge-red' : ($diasRestantes <= 7 ? 'badge-yellow' : 'badge-gray') }}">
                    {{ $diasRestantes === 0 ? 'hoje' : $diasRestantes . 'd' }}
                </span>
            </td>
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
            <td style="text-align:right; color:#2563eb;">{{ number_format($totalValor, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
