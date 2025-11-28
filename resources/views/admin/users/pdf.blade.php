<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Usuário</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #6b46c1; padding-bottom: 8px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: bold; color: #4c1d95; }
        .meta { font-size: 11px; color: #555; }
        .section { margin-bottom: 18px; }
        .section h3 { margin: 0 0 8px 0; font-size: 14px; border-left: 4px solid #6b46c1; padding-left: 8px; color: #2d3748; }
        .grid { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; }
        .stats { display: table; width: 100%; }
        .stat { display: table-cell; text-align: center; padding: 8px; border: 1px solid #e2e8f0; }
        .stat h4 { margin: 0; font-size: 18px; color: #111; }
        .stat small { color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f7fafc; text-align: left; font-size: 12px; }
        .muted { color: #666; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; color: #fff; }
        .badge-info { background: #3b82f6; }
        .badge-warn { background: #f59e0b; }
        .badge-success { background: #10b981; }
        .badge-danger { background: #ef4444; }
        .footer { margin-top: 12px; font-size: 10px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Relatório do Usuário</div>
        <div class="meta">Gerado em: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    </div>

    <div class="section">
        <h3>Informações do Usuário</h3>
        <table>
            <tr>
                <th>Nome</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Função</th>
                <td>{{ ucfirst($user->role) }}</td>
            </tr>
            <tr>
                <th>Localização</th>
                <td>{{ $user->location->name ?? '—' }}</td>
            </tr>
            <tr>
                <th>Telefone</th>
                <td>{{ $user->phone ?? '—' }}</td>
            </tr>
            <tr>
                <th>Departamento</th>
                <td>{{ $user->department ?? '—' }}</td>
            </tr>
            <tr>
                <th>Cadastro</th>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Resumo</h3>
        <div class="stats">
            <div class="stat">
                <h4>{{ $stats['created_total'] }}</h4>
                <small>Tickets Criados</small>
            </div>
            <div class="stat">
                <h4>{{ $stats['assigned_total'] }}</h4>
                <small>Tickets Atribuídos</small>
            </div>
            <div class="stat">
                <h4>{{ $stats['in_progress'] }}</h4>
                <small>Em Andamento</small>
            </div>
            <div class="stat">
                <h4>{{ $stats['resolved'] }}</h4>
                <small>Resolvidos</small>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Últimos Tickets Criados</h3>
        <table>
            <thead>
                <tr>
                    <th width="12%">Ticket</th>
                    <th>Título</th>
                    <th width="18%">Categoria</th>
                    <th width="16%">Status</th>
                    <th width="18%">Criado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCreated as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td>{{ Str::limit($t->title, 60) }}</td>
                    <td class="muted">{{ $t->category->name ?? '—' }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'open' => ['Aberto','badge-info'],
                                'in_progress' => ['Em Andamento','badge-warn'],
                                'resolved' => ['Resolvido','badge-success'],
                                'closed' => ['Fechado','badge-danger'],
                            ];
                            [$label,$klass] = $statusMap[$t->status] ?? [$t->status,'badge-info'];
                        @endphp
                        <span class="badge {{ $klass }}">{{ $label }}</span>
                    </td>
                    <td class="muted">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="muted">Sem registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(in_array($user->role, ['technician','admin']))
    <div class="section">
        <h3>Últimos Tickets Atribuídos</h3>
        <table>
            <thead>
                <tr>
                    <th width="12%">Ticket</th>
                    <th>Título</th>
                    <th width="18%">Categoria</th>
                    <th width="18%">Cliente</th>
                    <th width="16%">Status</th>
                    <th width="18%">Atualizado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAssigned as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td>{{ Str::limit($t->title, 50) }}</td>
                    <td class="muted">{{ $t->category->name ?? '—' }}</td>
                    <td class="muted">{{ $t->user->name ?? '—' }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'open' => ['Aberto','badge-info'],
                                'in_progress' => ['Em Andamento','badge-warn'],
                                'resolved' => ['Resolvido','badge-success'],
                                'closed' => ['Fechado','badge-danger'],
                            ];
                            [$label,$klass] = $statusMap[$t->status] ?? [$t->status,'badge-info'];
                        @endphp
                        <span class="badge {{ $klass }}">{{ $label }}</span>
                    </td>
                    <td class="muted">{{ $t->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="muted">Sem registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">Relatório gerado automaticamente - Sistema de Chamados</div>
</body>
</html>
