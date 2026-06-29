<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\RelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    public function __construct(private RelatorioService $service) {}

    // ─── INADIMPLÊNCIA ────────────────────────────────────────────────────────

    public function inadimplenciaCsv()
    {
        $parcelas = $this->service->inadimplencia();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $callback = function () use ($parcelas) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($f, ['Cliente', 'Cobrança', 'Parcela', 'Vencimento', 'Dias em atraso', 'Valor (R$)'], ';');
            foreach ($parcelas as $p) {
                fputcsv($f, [
                    $p->cobranca->cliente->nome,
                    $p->cobranca->descricao,
                    $p->numero,
                    $p->vencimento->format('d/m/Y'),
                    now()->diffInDays($p->vencimento),
                    number_format($p->valor, 2, ',', '.'),
                ], ';');
            }
            fclose($f);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            'Content-Disposition' => 'attachment; filename="inadimplencia_' . now()->format('Ymd') . '.csv"',
        ]));
    }

    public function inadimplenciaPdf()
    {
        $parcelas = $this->service->inadimplencia();
        $empresa  = Auth::user()->empresa ?? null;
        $pdf = Pdf::loadView('pdf.inadimplencia', compact('parcelas', 'empresa'))->setPaper('a4', 'landscape');
        return $pdf->download('inadimplencia_' . now()->format('Ymd') . '.pdf');
    }

    // ─── RECEBIMENTOS ─────────────────────────────────────────────────────────

    public function recebimentosCsv(Request $request)
    {
        $request->validate([
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
            'cliente_id'  => ['nullable', 'exists:clientes,id'],
        ]);

        $parcelas = $this->service->recebimentos(
            $request->data_inicio,
            $request->data_fim,
            $request->cliente_id ?: null,
        );

        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $callback = function () use ($parcelas) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['Cliente', 'Cobrança', 'Parcela', 'Vencimento', 'Data Pagamento', 'Valor (R$)'], ';');
            foreach ($parcelas as $p) {
                fputcsv($f, [
                    $p->cobranca->cliente->nome,
                    $p->cobranca->descricao,
                    $p->numero,
                    $p->vencimento->format('d/m/Y'),
                    $p->data_pagamento->format('d/m/Y'),
                    number_format($p->valor, 2, ',', '.'),
                ], ';');
            }
            fclose($f);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            'Content-Disposition' => 'attachment; filename="recebimentos_' . now()->format('Ymd') . '.csv"',
        ]));
    }

    public function recebimentosPdf(Request $request)
    {
        $request->validate([
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
            'cliente_id'  => ['nullable', 'exists:clientes,id'],
        ]);

        $parcelas   = $this->service->recebimentos($request->data_inicio, $request->data_fim, $request->cliente_id ?: null);
        $empresa    = Auth::user()->empresa ?? null;
        $dataInicio = $request->data_inicio;
        $dataFim    = $request->data_fim;

        $pdf = Pdf::loadView('pdf.recebimentos', compact('parcelas', 'empresa', 'dataInicio', 'dataFim'))->setPaper('a4', 'landscape');
        return $pdf->download('recebimentos_' . now()->format('Ymd') . '.pdf');
    }

    // ─── FLUXO DE CAIXA ───────────────────────────────────────────────────────

    public function fluxoCaixaCsv(Request $request)
    {
        $dias     = (int) ($request->dias ?? 30);
        $parcelas = $this->service->fluxoCaixa($dias);

        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $callback = function () use ($parcelas) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['Cliente', 'Cobrança', 'Parcela', 'Vencimento', 'Valor (R$)'], ';');
            foreach ($parcelas as $p) {
                fputcsv($f, [
                    $p->cobranca->cliente->nome,
                    $p->cobranca->descricao,
                    $p->numero,
                    $p->vencimento->format('d/m/Y'),
                    number_format($p->valor, 2, ',', '.'),
                ], ';');
            }
            fclose($f);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            'Content-Disposition' => 'attachment; filename="fluxo_caixa_' . now()->format('Ymd') . '.csv"',
        ]));
    }

    public function fluxoCaixaPdf(Request $request)
    {
        $dias     = (int) ($request->dias ?? 30);
        $parcelas = $this->service->fluxoCaixa($dias);
        $empresa  = Auth::user()->empresa ?? null;

        $pdf = Pdf::loadView('pdf.fluxo-caixa', compact('parcelas', 'empresa', 'dias'))->setPaper('a4', 'landscape');
        return $pdf->download('fluxo_caixa_' . now()->format('Ymd') . '.pdf');
    }

    // ─── HISTÓRICO POR CLIENTE ────────────────────────────────────────────────

    public function historicoClienteCsv(Request $request)
    {
        $request->validate([
            'cliente_id'  => ['required', 'exists:clientes,id'],
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
        ]);

        $parcelas = $this->service->historicoCliente($request->cliente_id, $request->data_inicio, $request->data_fim);
        $cliente  = Cliente::findOrFail($request->cliente_id);

        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $callback = function () use ($parcelas) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['Cobrança', 'Parcela', 'Vencimento', 'Status', 'Data Pagamento', 'Valor (R$)'], ';');
            foreach ($parcelas as $p) {
                fputcsv($f, [
                    $p->cobranca->descricao,
                    $p->numero,
                    $p->vencimento->format('d/m/Y'),
                    ucfirst($p->status),
                    $p->data_pagamento?->format('d/m/Y') ?? '—',
                    number_format($p->valor, 2, ',', '.'),
                ], ';');
            }
            fclose($f);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            'Content-Disposition' => 'attachment; filename="historico_' . str($cliente->nome)->slug() . '_' . now()->format('Ymd') . '.csv"',
        ]));
    }

    public function historicoClientePdf(Request $request)
    {
        $request->validate([
            'cliente_id'  => ['required', 'exists:clientes,id'],
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
        ]);

        $parcelas   = $this->service->historicoCliente($request->cliente_id, $request->data_inicio, $request->data_fim);
        $cliente    = Cliente::findOrFail($request->cliente_id);
        $empresa    = Auth::user()->empresa ?? null;
        $dataInicio = $request->data_inicio;
        $dataFim    = $request->data_fim;

        $pdf = Pdf::loadView('pdf.historico-cliente', compact('parcelas', 'cliente', 'empresa', 'dataInicio', 'dataFim'))->setPaper('a4', 'landscape');
        return $pdf->download('historico_' . str($cliente->nome)->slug() . '_' . now()->format('Ymd') . '.pdf');
    }
}
