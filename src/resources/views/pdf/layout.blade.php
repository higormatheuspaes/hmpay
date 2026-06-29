<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
    .header { padding: 18px 24px 14px; border-bottom: 2px solid #4f46e5; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-end; }
    .header-left h1 { font-size: 16px; font-weight: 700; color: #4f46e5; }
    .header-left p { font-size: 10px; color: #6b7280; margin-top: 2px; }
    .header-right { text-align: right; font-size: 10px; color: #6b7280; }
    .content { padding: 0 24px 24px; }
    table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    thead th { background: #f3f4f6; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; }
    tbody tr:last-child td { border-bottom: none; }
    tfoot td { padding: 8px 10px; font-weight: 700; background: #f9fafb; border-top: 2px solid #e5e7eb; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 9px; font-weight: 600; }
    .badge-red    { background: #fee2e2; color: #dc2626; }
    .badge-green  { background: #d1fae5; color: #059669; }
    .badge-yellow { background: #fef3c7; color: #d97706; }
    .badge-gray   { background: #f3f4f6; color: #6b7280; }
    .summary { display: flex; gap: 16px; margin-bottom: 16px; }
    .summary-card { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; }
    .summary-card .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; font-weight: 600; }
    .summary-card .value { font-size: 15px; font-weight: 700; color: #111827; margin-top: 2px; }
</style>
</head>
<body>
<div class="header">
    <div class="header-left">
        <h1>HMPay</h1>
        <p>{{ $empresa?->nome ?? '' }}</p>
    </div>
    <div class="header-right">
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
<div class="content">
    @yield('content')
</div>
</body>
</html>
