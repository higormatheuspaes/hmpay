<?php

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Cliente $cliente;

    public function mount(Cliente $cliente): void
    {
        abort_if($cliente->empresa_id !== Auth::user()->empresa_id, 403);

        $this->cliente = $cliente->load([
            'cobrancas.parcelas',
            'scoreHistorico' => fn($q) => $q->latest()->limit(20),
            'scoreHistorico.parcela.cobranca',
        ]);
    }
}; ?>

<div>
    {{-- Cabeçalho --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clientes.index') }}" wire:navigate
            class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-semibold text-gray-900 truncate">{{ $cliente->nome }}</h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5">
                @if($cliente->telefone)
                    <span class="text-sm text-gray-500">{{ $cliente->telefone }}</span>
                @endif
                @if($cliente->cpf_cnpj)
                    <span class="text-sm text-gray-400">·</span>
                    <span class="text-sm text-gray-500">{{ $cliente->cpf_cnpj }}</span>
                @endif
                @if($cliente->email)
                    <span class="text-sm text-gray-400">·</span>
                    <span class="text-sm text-gray-500">{{ $cliente->email }}</span>
                @endif
            </div>
        </div>

        @php
            $scoreBadge = match($cliente->score_categoria) {
                'bom_pagador' => ['label' => 'Bom pagador', 'class' => 'bg-green-100 text-green-700'],
                'atencao'     => ['label' => 'Atenção',     'class' => 'bg-yellow-100 text-yellow-700'],
                'risco'       => ['label' => 'Risco',       'class' => 'bg-red-100 text-red-700'],
                default       => ['label' => 'Novo',        'class' => 'bg-gray-100 text-gray-600'],
            };
        @endphp
        <span class="text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0 {{ $scoreBadge['class'] }}">
            {{ $scoreBadge['label'] }} · {{ $cliente->score_atual }} pts
        </span>
    </div>

    {{-- Cards de resumo --}}
    @php
        $todasParcelas   = $cliente->cobrancas->flatMap->parcelas;
        $totalParcelas   = $todasParcelas->count();
        $pagas           = $todasParcelas->where('status', 'pago')->count();
        $emAtraso        = $todasParcelas->filter(fn($p) => $p->status === 'atrasado' || ($p->status === 'pendente' && $p->vencimento->isPast()))->count();
        $valorTotal      = $todasParcelas->sum('valor');
        $valorPago       = $todasParcelas->where('status', 'pago')->sum('valor');
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Cobranças</p>
            <p class="text-2xl font-bold text-gray-900">{{ $cliente->cobrancas->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Parcelas pagas</p>
            <p class="text-2xl font-bold text-green-600">{{ $pagas }}<span class="text-sm font-normal text-gray-400">/{{ $totalParcelas }}</span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Em atraso</p>
            <p class="text-2xl font-bold {{ $emAtraso > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $emAtraso }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Valor pago</p>
            <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($valorPago, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400">de R$ {{ number_format($valorTotal, 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Grid principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Cobranças (col 2/3) --}}
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Cobranças</h2>

            @forelse($cliente->cobrancas->sortByDesc('created_at') as $cobranca)
                @php
                    $totalParcCobranca = $cobranca->parcelas->count();
                    $pagasCobranca     = $cobranca->parcelas->where('status', 'pago')->count();
                    $temAtraso         = $cobranca->parcelas->contains(fn($p) => $p->status === 'atrasado' || ($p->status === 'pendente' && $p->vencimento->isPast()));
                    $statusCobranca    = match(true) {
                        $temAtraso => ['label' => 'Em atraso', 'class' => 'bg-red-100 text-red-700'],
                        $pagasCobranca === $totalParcCobranca && $totalParcCobranca > 0 => ['label' => 'Quitada',   'class' => 'bg-green-100 text-green-700'],
                        default => ['label' => 'Em aberto', 'class' => 'bg-yellow-100 text-yellow-700'],
                    };
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    {{-- Header da cobrança --}}
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $cobranca->descricao }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $pagasCobranca }}/{{ $totalParcCobranca }} parcelas ·
                                    R$ {{ number_format($cobranca->valor_total, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0 {{ $statusCobranca['class'] }}">
                            {{ $statusCobranca['label'] }}
                        </span>
                    </div>

                    {{-- Parcelas --}}
                    <div class="divide-y divide-gray-50">
                        @foreach($cobranca->parcelas->sortBy('numero') as $parcela)
                            @php
                                $hoje = now()->startOfDay();
                                $parcelaStatus = $parcela->status;
                                $vencida    = $parcelaStatus === 'pendente' && $parcela->vencimento->isPast();
                                $statusInfo = match(true) {
                                    $parcelaStatus === 'pago'     => ['dot' => 'bg-green-500', 'label' => 'Pago',      'valor' => 'text-green-700'],
                                    $parcelaStatus === 'atrasado' || $vencida => ['dot' => 'bg-red-500', 'label' => 'Atrasado', 'valor' => 'text-red-700'],
                                    default => ['dot' => 'bg-gray-300', 'label' => 'Em aberto', 'valor' => 'text-gray-700'],
                                };
                            @endphp
                            <div class="flex items-center gap-3 px-5 py-3">
                                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $statusInfo['dot'] }}"></div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm text-gray-700">Parcela {{ $parcela->numero }}/{{ $totalParcCobranca }}</span>
                                    <span class="text-xs text-gray-400 ml-2">
                                        vence {{ $parcela->vencimento->format('d/m/Y') }}
                                        @if($parcela->data_pagamento)
                                            · pago em {{ $parcela->data_pagamento->format('d/m/Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold {{ $statusInfo['valor'] }}">
                                        R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $statusInfo['label'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm text-gray-400">Nenhuma cobrança registrada</p>
                </div>
            @endforelse
        </div>

        {{-- Score (col 1/3) --}}
        <div class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Score</h2>

            {{-- Score atual visual --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                @php
                    $score       = max(0, min(100, $cliente->score_atual));
                    $scoreColor  = match(true) {
                        $score >= 70 => ['bar' => 'bg-green-500',  'text' => 'text-green-600'],
                        $score >= 40 => ['bar' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
                        default      => ['bar' => 'bg-red-500',    'text' => 'text-red-600'],
                    };
                @endphp
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Pontuação atual</p>
                        <p class="text-3xl font-bold {{ $scoreColor['text'] }}">{{ $cliente->score_atual }}</p>
                    </div>
                    <p class="text-xs text-gray-400 mb-1">/ 100</p>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full transition-all {{ $scoreColor['bar'] }}" style="width: {{ $score }}%"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-xs text-gray-400">Risco</span>
                    <span class="text-xs text-gray-400">Bom pagador</span>
                </div>
            </div>

            {{-- Histórico de score --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">Histórico de pontos</p>
                </div>

                @if($cliente->scoreHistorico->isEmpty())
                    <div class="px-5 py-6 text-center">
                        <p class="text-xs text-gray-400">Nenhuma movimentação ainda.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($cliente->scoreHistorico as $historico)
                            <div class="flex items-start gap-3 px-5 py-3">
                                <span class="text-sm font-bold flex-shrink-0 w-12 text-right {{ $historico->pontos_aplicados >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $historico->pontos_aplicados > 0 ? '+' : '' }}{{ $historico->pontos_aplicados }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-700 truncate">
                                        @if($historico->parcela?->cobranca)
                                            {{ $historico->parcela->cobranca->descricao }}
                                            · parc. {{ $historico->parcela->numero }}
                                        @else
                                            Ajuste manual
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $historico->created_at->format('d/m/Y') }} · resultado: {{ $historico->score_resultante }} pts</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
