<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Executivo - Relatório</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .metric-card h3 {
            color: #495057;
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .metric-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }
        .metric-card .description {
            color: #6c757d;
            font-size: 12px;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }
        .priority-medium {
            color: #ffc107;
            font-weight: bold;
        }
        .priority-low {
            color: #28a745;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Executivo - Chamados</h1>
        <p>Relatório gerado em {{ $generatedAt }}</p>
    </div>

    <!-- Métricas Principais -->
    <div class="metrics-grid">
        <div class="metric-card">
            <h3>Total de Chamados</h3>
            <div class="value">{{ $data->total_tickets }}</div>
            <div class="description">Todos os chamados do sistema</div>
        </div>
        <div class="metric-card">
            <h3>Chamados Abertos</h3>
            <div class="value">{{ $data->open_tickets }}</div>
            <div class="description">Aguardando atendimento</div>
        </div>
        <div class="metric-card">
            <h3>Resolvidos Hoje</h3>
            <div class="value">{{ $data->resolved_today }}</div>
            <div class="description">Chamados concluídos hoje</div>
        </div>
        <div class="metric-card">
            <h3>Tempo Médio</h3>
            <div class="value">{{ $data->avg_resolution_time }}h</div>
            <div class="description">Para resolução</div>
        </div>
    </div>

    <div class="two-column">
        <!-- Chamados por Categoria -->
        <div class="section">
            <h2>Chamados por Categoria</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Chamados por Prioridade -->
        <div class="section">
            <h2>Chamados por Prioridade</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Prioridade</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="priority-high">Alta</td>
                        <td>{{ $data->priorities->high }}</td>
                    </tr>
                    <tr>
                        <td class="priority-medium">Média</td>
                        <td>{{ $data->priorities->medium }}</td>
                    </tr>
                    <tr>
                        <td class="priority-low">Baixa</td>
                        <td>{{ $data->priorities->low }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tendências dos Últimos 7 Dias -->
    <div class="section">
        <h2>Tendências dos Últimos 7 Dias</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Novos Chamados</th>
                    <th>Chamados Resolvidos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->trends->labels as $index => $date)
                <tr>
                    <td>{{ $date }}</td>
                    <td>{{ $data->trends->created[$index] }}</td>
                    <td>{{ $data->trends->resolved[$index] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Desempenho dos Atendentes -->
    <div class="section">
        <h2>Desempenho dos Atendentes (Últimos 30 dias)</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Atendente</th>
                    <th>Chamados Resolvidos</th>
                    <th>Tempo Médio (horas)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->agent_performance as $agent)
                <tr>
                    <td>{{ $agent->name }}</td>
                    <td>{{ $agent->resolved }}</td>
                    <td>{{ $agent->avg_time }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Sistema de Chamados - Relatório gerado automaticamente</p>
        <p>Este relatório contém informações confidenciais e é destinado apenas para uso interno.</p>
    </div>
</body>
</html>
