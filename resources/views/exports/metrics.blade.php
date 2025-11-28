<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        h2 { font-size: 16px; margin: 18px 0 8px; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 6px; color: #fff; font-weight: 600; font-size: 11px; }
        .urgent { background: #ef4444; }
        .high { background: #f59e0b; }
        .medium { background: #3b82f6; }
        .low { background: #10b981; }
        .header { display:flex; justify-content: space-between; align-items: baseline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $appName }} - Métricas</h1>
        <div class="muted">Gerado em {{ $metrics['generated_at'] }}</div>
    </div>

    <h2>Resumo</h2>
    <table>
        <tbody>
            <tr><th>Hoje</th><td>{{ $metrics['today'] }}</td></tr>
            <tr><th>Ontem</th><td>{{ $metrics['yesterday'] }}</td></tr>
            <tr><th>Tempo médio de resolução (h)</th><td>{{ $metrics['avg_resolution_time'] }}</td></tr>
        </tbody>
    </table>

    <h2>Por Prioridade</h2>
    <table>
        <thead><tr><th>Prioridade</th><th>Quantidade</th></tr></thead>
        <tbody>
            <tr><td><span class="badge urgent">Urgente</span></td><td>{{ $metrics['priority']['urgent'] }}</td></tr>
            <tr><td><span class="badge high">Alta</span></td><td>{{ $metrics['priority']['high'] }}</td></tr>
            <tr><td><span class="badge medium">Média</span></td><td>{{ $metrics['priority']['medium'] }}</td></tr>
            <tr><td><span class="badge low">Baixa</span></td><td>{{ $metrics['priority']['low'] }}</td></tr>
        </tbody>
    </table>
</body>
</html>
