<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Métricas de Chamados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
            margin-top: 0;
        }
        .metrics-section {
            margin-bottom: 30px;
        }
        .metrics-section h2 {
            color: #0d6efd;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .metrics-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .metrics-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #495057;
        }
        .metrics-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .metrics-label {
            font-weight: bold;
        }
        .metrics-value {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
        }
        .high { color: #dc3545; }
        .medium { color: #ffc107; }
        .low { color: #28a745; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Métricas de Chamados</h1>
        <p>Gerado em: {{ $metrics['generated_at'] }}</p>
    </div>
    
    <div class="metrics-section">
        <h2>Chamados por Período</h2>
        <table>
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Novos</th>
                    <th>Resolvidos</th>
                    <th>Fechados</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Hoje</strong></td>
                    <td>{{ $metrics['today']['created'] }}</td>
                    <td>{{ $metrics['today']['resolved'] }}</td>
                    <td>{{ $metrics['today']['closed'] }}</td>
                </tr>
                <tr>
                    <td><strong>Ontem</strong></td>
                    <td>{{ $metrics['yesterday']['created'] }}</td>
                    <td>{{ $metrics['yesterday']['resolved'] }}</td>
                    <td>{{ $metrics['yesterday']['closed'] }}</td>
                </tr>
                <tr>
                    <td><strong>Esta semana</strong></td>
                    <td>{{ $metrics['week']['created'] }}</td>
                    <td>{{ $metrics['week']['resolved'] }}</td>
                    <td>{{ $metrics['week']['closed'] }}</td>
                </tr>
                <tr>
                    <td><strong>Semana passada</strong></td>
                    <td>{{ $metrics['last_week']['created'] }}</td>
                    <td>{{ $metrics['last_week']['resolved'] }}</td>
                    <td>{{ $metrics['last_week']['closed'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="metrics-section">
        <h2>Distribuição por Prioridade (Tickets Abertos)</h2>
        <div class="metrics-card">
            <div class="metrics-row">
                <span class="metrics-label high">Alta:</span>
                <span class="metrics-value">{{ $metrics['priority_distribution']['high'] }}</span>
            </div>
            <div class="metrics-row">
                <span class="metrics-label medium">Média:</span>
                <span class="metrics-value">{{ $metrics['priority_distribution']['medium'] }}</span>
            </div>
            <div class="metrics-row">
                <span class="metrics-label low">Baixa:</span>
                <span class="metrics-value">{{ $metrics['priority_distribution']['low'] }}</span>
            </div>
            <div class="metrics-row">
                <span class="metrics-label">Total:</span>
                <span class="metrics-value">{{ $metrics['priority_distribution']['high'] + $metrics['priority_distribution']['medium'] + $metrics['priority_distribution']['low'] }}</span>
            </div>
        </div>
    </div>
    
    <div class="metrics-section">
        <h2>Desempenho</h2>
        <div class="metrics-card">
            <div class="metrics-row">
                <span class="metrics-label">Tempo médio de resolução:</span>
                <span class="metrics-value">{{ $metrics['avg_resolution_time'] }} horas</span>
            </div>
            <div class="metrics-row">
                <span class="metrics-label">Conformidade com SLA:</span>
                <span class="metrics-value">{{ $metrics['sla_compliance'] }}%</span>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo Sistema de Chamados.</p>
        <p>&copy; {{ date('Y') }} - Todos os direitos reservados</p>
    </div>
</body>
</html>
