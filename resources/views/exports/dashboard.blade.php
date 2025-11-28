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
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    </style>
</head>
<body>
    <div class="header" style="display:flex; justify-content: space-between; align-items: baseline;">
        <h1>{{ $appName }} - Dashboard</h1>
        <div class="muted">Gerado em {{ $data['generated_at'] }}</div>
    </div>

    <h2>Resumo</h2>
    <table>
        <tbody>
            <tr><th>Total</th><td>{{ $data['totals']['total'] }}</td></tr>
            <tr><th>Abertos</th><td>{{ $data['totals']['open'] }}</td></tr>
            <tr><th>Em andamento</th><td>{{ $data['totals']['inProgress'] }}</td></tr>
            <tr><th>Resolvidos hoje</th><td>{{ $data['totals']['resolvedToday'] }}</td></tr>
            <tr><th>Tempo médio de resolução (h)</th><td>{{ $data['totals']['avgTime'] }}</td></tr>
        </tbody>
    </table>

    <div class="grid">
        <div>
            <h2>Por Categoria</h2>
            <table>
                <thead><tr><th>Categoria</th><th>Qtd</th></tr></thead>
                <tbody>
                @foreach ($data['byCategory'] as $row)
                    <tr>
                        <td>{{ optional(\App\Models\Category::find($row->category_id))->name ?? 'Sem categoria' }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <h2>Por Prioridade</h2>
            <table>
                <thead><tr><th>Prioridade</th><th>Qtd</th></tr></thead>
                <tbody>
                @foreach ($data['byPriority'] as $row)
                    <tr>
                        <td>{{ ucfirst($row->priority) }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
