<?php

use App\Services\RelatorioService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function with(): array
    {
        return [
            'clientes' => (new RelatorioService)->clientes(),
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-semibold text-gray-900">Relatórios</h1>
        <p class="text-sm text-gray-500 mt-0.5">Exporte seus dados em CSV ou PDF</p>
    </div>

    {{-- Inadimplência --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Relatório de Inadimplência</h2>
                <p class="text-sm text-gray-500 mt-0.5">Todas as parcelas vencidas e não pagas, com dias de atraso</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('relatorios.inadimplencia.csv') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar CSV
            </a>
            <a href="{{ route('relatorios.inadimplencia.pdf') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </a>
        </div>
    </div>

    {{-- Recebimentos --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Extrato de Recebimentos</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pagamentos recebidos em um período, com opção de filtrar por cliente</p>
            </div>
        </div>
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4" id="form-recebimentos">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Data início *</label>
                <input type="date" name="data_inicio" required
                    value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Data fim *</label>
                <input type="date" name="data_fim" required
                    value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cliente (opcional)</label>
                <select name="cliente_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Todos os clientes</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="flex gap-2">
            <button type="button" onclick="exportar('recebimentos', 'csv')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar CSV
            </button>
            <button type="button" onclick="exportar('recebimentos', 'pdf')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </button>
        </div>
    </div>

    {{-- Fluxo de caixa --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Fluxo de Caixa Previsto</h2>
                <p class="text-sm text-gray-500 mt-0.5">Parcelas a vencer nos próximos dias — planejamento de recebimentos</p>
            </div>
        </div>
        <form class="mb-4" id="form-fluxo-caixa">
            <div class="max-w-xs">
                <label class="block text-xs font-medium text-gray-600 mb-1">Período</label>
                <select name="dias"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="7">Próximos 7 dias</option>
                    <option value="15">Próximos 15 dias</option>
                    <option value="30" selected>Próximos 30 dias</option>
                    <option value="60">Próximos 60 dias</option>
                    <option value="90">Próximos 90 dias</option>
                </select>
            </div>
        </form>
        <div class="flex gap-2">
            <button type="button" onclick="exportar('fluxo-caixa', 'csv')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar CSV
            </button>
            <button type="button" onclick="exportar('fluxo-caixa', 'pdf')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </button>
        </div>
    </div>

    {{-- Histórico por cliente --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Histórico por Cliente</h2>
                <p class="text-sm text-gray-500 mt-0.5">Todas as cobranças e parcelas de um cliente em um período</p>
            </div>
        </div>
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4" id="form-historico-cliente">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cliente *</label>
                <select name="cliente_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Selecione um cliente</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Data início *</label>
                <input type="date" name="data_inicio" required
                    value="{{ now()->startOfYear()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Data fim *</label>
                <input type="date" name="data_fim" required
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </form>
        <div class="flex gap-2">
            <button type="button" onclick="exportar('historico-cliente', 'csv')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar CSV
            </button>
            <button type="button" onclick="exportar('historico-cliente', 'pdf')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </button>
        </div>
    </div>

</div>

<script>
const rotas = {
    'recebimentos':    { csv: '{{ route('relatorios.recebimentos.csv') }}',      pdf: '{{ route('relatorios.recebimentos.pdf') }}' },
    'fluxo-caixa':    { csv: '{{ route('relatorios.fluxo-caixa.csv') }}',       pdf: '{{ route('relatorios.fluxo-caixa.pdf') }}' },
    'historico-cliente': { csv: '{{ route('relatorios.historico-cliente.csv') }}', pdf: '{{ route('relatorios.historico-cliente.pdf') }}' },
};

function exportar(relatorio, formato) {
    const form = document.getElementById('form-' + relatorio);
    if (!form) return;
    const params = new URLSearchParams(new FormData(form));
    window.location.href = rotas[relatorio][formato] + '?' + params.toString();
}
</script>
