<?php

use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'dashboard')->name('dashboard');
    Volt::route('clientes', 'clientes/index')->name('clientes.index');
    Volt::route('cobrancas', 'cobrancas/index')->name('cobrancas.index');
    Volt::route('cobrancas/create', 'cobrancas/create')->name('cobrancas.create');
    Volt::route('cobrancas/{cobranca}', 'cobrancas/show')->name('cobrancas.show');
    Volt::route('parcelas', 'parcelas/index')->name('parcelas.index');
    Volt::route('relatorios', 'relatorios/index')->name('relatorios.index');
    Volt::route('configuracoes', 'configuracoes/index')->name('configuracoes.index');
    Volt::route('clientes/{cliente}', 'clientes/show')->name('clientes.show');

    Route::get('relatorios/inadimplencia/csv',          [RelatorioController::class, 'inadimplenciaCsv'])->name('relatorios.inadimplencia.csv');
    Route::get('relatorios/inadimplencia/pdf',          [RelatorioController::class, 'inadimplenciaPdf'])->name('relatorios.inadimplencia.pdf');
    Route::get('relatorios/recebimentos/csv',           [RelatorioController::class, 'recebimentosCsv'])->name('relatorios.recebimentos.csv');
    Route::get('relatorios/recebimentos/pdf',           [RelatorioController::class, 'recebimentosPdf'])->name('relatorios.recebimentos.pdf');
    Route::get('relatorios/fluxo-caixa/csv',            [RelatorioController::class, 'fluxoCaixaCsv'])->name('relatorios.fluxo-caixa.csv');
    Route::get('relatorios/fluxo-caixa/pdf',            [RelatorioController::class, 'fluxoCaixaPdf'])->name('relatorios.fluxo-caixa.pdf');
    Route::get('relatorios/historico-cliente/csv',      [RelatorioController::class, 'historicoClienteCsv'])->name('relatorios.historico-cliente.csv');
    Route::get('relatorios/historico-cliente/pdf',      [RelatorioController::class, 'historicoClientePdf'])->name('relatorios.historico-cliente.pdf');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
