<?php

use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function with(): array
    {
        $empresaId = Auth::user()->empresa_id;

        $totalClientes = Cliente::where('empresa_id', $empresaId)->count();

        $valorAReceber = Parcela::whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '>=', now())
            ->sum('valor');

        $totalAtrasadas = Parcela::whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '<', now())
            ->count();

        $valorAtrasado = Parcela::whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '<', now())
            ->sum('valor');

        $recebidoMes = Parcela::whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pago')
            ->whereMonth('data_pagamento', now()->month)
            ->whereYear('data_pagamento', now()->year)
            ->sum('valor');

        $proximasVencer = Parcela::with(['cobranca.cliente'])
            ->whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '>=', now())
            ->whereDate('vencimento', '<=', now()->addDays(30))
            ->orderBy('vencimento')
            ->limit(8)
            ->get();

        $ultimasAtrasadas = Parcela::with(['cobranca.cliente'])
            ->whereHas('cobranca', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('status', 'pendente')
            ->whereDate('vencimento', '<', now())
            ->orderBy('vencimento')
            ->limit(8)
            ->get();

        return compact(
            'totalClientes',
            'valorAReceber',
            'totalAtrasadas',
            'valorAtrasado',
            'recebidoMes',
            'proximasVencer',
            'ultimasAtrasadas',
        );
    }
}; ?>

<div class="space-y-6">

    {{-- Saudação --}}
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Olá, {{ Auth::user()->name }} 👋</h1>
        <p class="text-sm text-gray-500 mt-0.5">Aqui está o resumo da sua operação hoje.</p>
    </div>

    {{-- Cards de métricas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Clientes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalClientes }}</p>
            <a href="{{ route('clientes.index') }}" wire:navigate class="text-xs text-indigo-600 hover:text-indigo-700 mt-2 inline-block">Ver todos →</a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">A receber</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($valorAReceber, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">próximos 30 dias</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 {{ $totalAtrasadas > 0 ? 'border-red-200 bg-red-50' : '' }}">
            <p class="text-xs font-medium {{ $totalAtrasadas > 0 ? 'text-red-400' : 'text-gray-400' }} uppercase tracking-wide">Atrasadas</p>
            <p class="text-2xl font-bold {{ $totalAtrasadas > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $totalAtrasadas }}</p>
            @if($totalAtrasadas > 0)
                <p class="text-xs text-red-400 mt-2">R$ {{ number_format($valorAtrasado, 2, ',', '.') }} em aberto</p>
            @else
                <p class="text-xs text-gray-400 mt-2">Tudo em dia</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Recebido este mês</p>
            <p class="text-2xl font-bold text-green-600 mt-1">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</p>
            <a href="{{ route('parcelas.index') }}" wire:navigate class="text-xs text-indigo-600 hover:text-indigo-700 mt-2 inline-block">Ver parcelas →</a>
        </div>

    </div>

    {{-- Tabelas lado a lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Atrasadas --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Parcelas atrasadas</h2>
                @if($totalAtrasadas > 8)
                    <a href="{{ route('parcelas.index') }}" wire:navigate class="text-xs text-indigo-600 hover:text-indigo-700">Ver todas</a>
                @endif
            </div>

            @if($ultimasAtrasadas->isEmpty())
                <div class="px-5 py-10 text-center">
                    <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-400">Nenhuma parcela atrasada</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($ultimasAtrasadas as $parcela)
                        <li class="px-5 py-3 flex items-center justify-between hover:bg-red-50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $parcela->cobranca->cliente->nome }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $parcela->cobranca->descricao }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <p class="text-sm font-semibold text-red-600">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</p>
                                <p class="text-xs text-red-400">{{ $parcela->vencimento->format('d/m/Y') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Próximas a vencer --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Próximas a vencer <span class="text-gray-400 font-normal">(30 dias)</span></h2>
                @if($proximasVencer->count() === 8)
                    <a href="{{ route('parcelas.index') }}" wire:navigate class="text-xs text-indigo-600 hover:text-indigo-700">Ver todas</a>
                @endif
            </div>

            @if($proximasVencer->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-400">Nenhuma parcela nos próximos 30 dias</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($proximasVencer as $parcela)
                        @php $diasRestantes = (int) now()->startOfDay()->diffInDays($parcela->vencimento->startOfDay(), false); @endphp
                        <li class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $parcela->cobranca->cliente->nome }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $parcela->cobranca->descricao }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <p class="text-sm font-semibold text-gray-800">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</p>
                                <p class="text-xs {{ $diasRestantes <= 3 ? 'text-orange-500' : 'text-gray-400' }}">
                                    {{ $parcela->vencimento->format('d/m/Y') }}
                                    @if($diasRestantes === 0)
                                        · hoje
                                    @elseif($diasRestantes <= 3)
                                        · {{ $diasRestantes }}d
                                    @endif
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>

</div>
