<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-visualização do Relatório do Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
  /* Reset básico */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
  
  /* Layout otimizado para A4 */
  @page { size: A4; margin: 15mm; }
  
  .report-container { 
    max-width: 210mm; 
    margin: 0 auto; 
    background: #fff; 
    font-size: 11px;
    page-break-inside: avoid;
  }
  
  .report-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 12px 16px; 
    background: linear-gradient(135deg, #10b981, #3b82f6); 
    color: #fff; 
    margin-bottom: 12px;
  }
  
  .report-title { margin: 0; font-size: 16px; font-weight: 700; }
  .report-meta { font-size: 10px; opacity: .95; margin-top: 2px; }
  .report-body { padding: 0 16px 12px 16px; }
  
  .toolbar { 
    position: sticky; 
    top: 0; 
    z-index: 20; 
    display: flex; 
    gap: 8px; 
    justify-content: flex-end; 
    padding: 10px 12px; 
    background: rgba(255,255,255,.95); 
    backdrop-filter: blur(6px); 
    border-bottom: 1px solid #e5e7eb; 
  }
  
  .toolbar .btn { 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    border-radius: 8px; 
    border: 1px solid #e5e7eb; 
    padding: 8px 12px; 
    background: #fff; 
    color: #111827; 
    font-weight: 600; 
    text-decoration: none; 
    box-shadow: 0 1px 2px rgba(0,0,0,.04); 
    transition: all 0.2s;
    font-size: 12px;
  }
  
  .toolbar .btn.primary { background: #111827; color: #fff; border-color: #111827; }
  .toolbar .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.08); }

  /* Seção compacta de Filtros */
  .filters-section { 
    margin-bottom: 10px; 
    padding: 8px 12px; 
    background: #f8fafc; 
    border-radius: 6px; 
    border: 1px solid #e2e8f0; 
  }
  
  .filters-section h3 { 
    margin: 0 0 6px 0; 
    font-size: 10px; 
    font-weight: 700; 
    color: #475569; 
    text-transform: uppercase; 
    letter-spacing: 0.3px; 
  }
  
  .filters { display: flex; flex-wrap: wrap; gap: 4px; }
  .chip { 
    display: inline-flex; 
    align-items: center; 
    gap: 4px; 
    background: #fff; 
    color: #3730a3; 
    border: 1px solid #c7d2fe; 
    padding: 3px 8px; 
    border-radius: 9999px; 
    font-size: 9px; 
    font-weight: 500; 
  }
  
  .chip i { font-size: 8px; }

  /* KPIs compactos */
  .kpis-section { margin-bottom: 12px; }
  .kpis-section h3 { margin: 0 0 8px 0; font-size: 11px; font-weight: 700; color: #1e293b; }
  .kpis { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
  .kpi { 
    border: 1px solid #e2e8f0; 
    border-radius: 6px; 
    padding: 8px 10px; 
    background: linear-gradient(145deg, #ffffff, #f8fafc); 
    box-shadow: 0 1px 2px rgba(0,0,0,0.03); 
  }
  
  .kpi .label { 
    font-size: 8px; 
    color: #64748b; 
    text-transform: uppercase; 
    letter-spacing: 0.3px; 
    font-weight: 600; 
    line-height: 1.2;
  }
  
  .kpi .value { 
    font-size: 18px; 
    font-weight: 800; 
    margin-top: 4px; 
    color: #0f172a; 
    line-height: 1; 
  }

  /* Tabelas compactas */
  .tables-section { margin-top: 12px; }
  .tables-section h3 { margin: 0 0 8px 0; font-size: 11px; font-weight: 700; color: #1e293b; }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .card { 
    border: 1px solid #e2e8f0; 
    border-radius: 6px; 
    background: #fff; 
    box-shadow: 0 1px 2px rgba(0,0,0,0.03); 
  }
  
  .card .card-header { 
    padding: 6px 10px; 
    border-bottom: 1px solid #e2e8f0; 
    font-weight: 700; 
    font-size: 10px; 
    color: #1e293b; 
    background: #f8fafc; 
    display: flex; 
    align-items: center; 
    gap: 4px; 
  }
  
  .card .card-header i { color: #3b82f6; font-size: 10px; }
  .card .card-body { padding: 0; max-height: 180px; overflow-y: auto; }
  table { width: 100%; border-collapse: collapse; font-size: 10px; }
  th, td { text-align: left; padding: 5px 10px; }
  th { 
    color: #64748b; 
    font-weight: 600; 
    background: #f8fafc; 
    text-transform: uppercase; 
    font-size: 8px; 
    letter-spacing: 0.3px; 
    position: sticky;
    top: 0;
  }
  
  td { color: #334155; border-bottom: 1px solid #f1f5f9; }
  tr:last-child td { border-bottom: none; }
  td:last-child { font-weight: 600; color: #0f172a; text-align: right; }

  /* Rodapé compacto */
  .report-footer { 
    padding: 8px 16px; 
    color: #64748b; 
    font-size: 8px; 
    text-align: center; 
    border-top: 1px solid #e2e8f0; 
    background: #f8fafc; 
    margin-top: 10px;
  }

  /* Impressão otimizada para 1 página */
  @media print {
    @page { size: A4; margin: 10mm; }
    html, body { height: 100%; page-break-after: avoid; }
    .toolbar { display: none !important; }
    .report-container { 
      box-shadow: none; 
      margin: 0; 
      border-radius: 0;
      max-width: 100%;
      page-break-inside: avoid;
      page-break-after: avoid;
    }
    body { background: #fff; margin: 0; padding: 0; }
    .kpi:hover { transform: none; box-shadow: none; }
    .card .card-body { max-height: none; overflow: visible; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>
<body>

<div class="toolbar no-print">
  @php
    // Reconstrói query atual para repassar aos links de download
    $qs = http_build_query(request()->query());
  @endphp
  <a class="btn" href="{{ url()->previous() }}">
    <i class="bi bi-arrow-left"></i> Voltar
  </a>
  <a class="btn" href="{{ route('dashboard.export') . ($qs ? ('?'.$qs) : '') }}" target="_blank">
    <i class="bi bi-download"></i> Baixar PDF
  </a>
  <button class="btn primary" onclick="window.print()">
    <i class="bi bi-printer"></i> Imprimir
  </button>
</div>

<div class="report-container">
  <div class="report-header">
    <div>
      <h1 class="report-title">{{ $technicianName ? 'Relatório - ' . $technicianName : 'Relatório do Dashboard' }}</h1>
      <div class="report-meta">{{ $appName ?? config('app.name') }} • Gerado em {{ $data['generated_at'] }}</div>
    </div>
    <div style="text-align:right">
      <div style="font-weight:800;font-size:14px;line-height:1.2">Suporte+ <span style="color:#f59e0b">Saúde</span></div>
      <div style="font-size:9px;opacity:.9">{{ $technicianName ? 'Atendimentos Individuais' : 'Resumo Executivo' }}</div>
    </div>
  </div>
  <div class="report-body">
    <!-- Filtros Aplicados -->
    <div class="filters-section">
      <h3><i class="bi bi-funnel"></i> Filtros Aplicados</h3>
      @if(!empty($filters))
        <div class="filters">
          @foreach($filters as $k => $v)
            <span class="chip"><i class="bi bi-check-circle-fill"></i> <strong>{{ ucfirst(str_replace('_',' ',$k)) }}:</strong> {{ $v }}</span>
          @endforeach
        </div>
      @else
        <div class="filters"><span class="chip" style="background:#ecfeff;color:#0891b2;border-color:#cffafe"><i class="bi bi-info-circle-fill"></i> Nenhum filtro aplicado (exibindo todos os dados)</span></div>
      @endif
    </div>

    <!-- KPIs -->
    <div class="kpis-section">
      <h3><i class="bi bi-graph-up"></i> Indicadores Principais</h3>
      <div class="kpis">
        <div class="kpi">
          <div class="label">Total</div>
          <div class="value">{{ $data['totals']['total'] }}</div>
        </div>
        <div class="kpi">
          <div class="label">Abertos</div>
          <div class="value">{{ $data['totals']['open'] }}</div>
        </div>
        <div class="kpi">
          <div class="label">Em Andamento</div>
          <div class="value">{{ $data['totals']['inProgress'] }}</div>
        </div>
        <div class="kpi">
          <div class="label">Resolvidos Hoje</div>
          <div class="value">{{ $data['totals']['resolvedToday'] }}</div>
        </div>
        <div class="kpi">
          <div class="label">Tempo Médio</div>
          <div class="value">{{ number_format($data['totals']['avgTime'], 1) }}<span style="font-size:14px;font-weight:500;color:#64748b">h</span></div>
        </div>
      </div>
    </div>

    <!-- Tabelas -->
    <div class="tables-section">
      <h3><i class="bi bi-table"></i> Análise Detalhada</h3>
      <div class="grid-2">
        <div class="card">
          <div class="card-header">
            <i class="bi bi-tag-fill"></i>
            Chamados por Categoria
          </div>
          <div class="card-body">
            <table>
              <thead>
                <tr>
                  <th>Categoria</th>
                  <th style="text-align:right">Quantidade</th>
                </tr>
              </thead>
              <tbody>
              @forelse($data['byCategory'] as $row)
                @php $name = optional(\App\Models\Category::find($row->category_id))->name ?? 'Sem categoria'; @endphp
                <tr>
                  <td>{{ $name }}</td>
                  <td>{{ $row->total }}</td>
                </tr>
              @empty
                <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:24px;">Sem dados para os filtros aplicados</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Chamados por Prioridade
          </div>
          <div class="card-body">
            <table>
              <thead>
                <tr>
                  <th>Prioridade</th>
                  <th style="text-align:right">Quantidade</th>
                </tr>
              </thead>
              <tbody>
              @forelse($data['byPriority'] as $row)
                <tr>
                  <td>
                    @if($row->priority === 'urgent')
                      <span style="color:#dc2626;font-weight:600">🔴 Urgente</span>
                    @elseif($row->priority === 'high')
                      <span style="color:#f59e0b;font-weight:600">🟠 Alta</span>
                    @elseif($row->priority === 'medium')
                      <span style="color:#3b82f6;font-weight:600">🔵 Média</span>
                    @else
                      <span style="color:#10b981;font-weight:600">🟢 Baixa</span>
                    @endif
                  </td>
                  <td>{{ $row->total }}</td>
                </tr>
              @empty
                <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:24px;">Sem dados para os filtros aplicados</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="report-footer">
    <div style="margin-bottom:8px;font-weight:600;color:#475569;">📄 Relatório Gerado Automaticamente</div>
    <div>{{ $appName ?? config('app.name') }} • {{ $data['generated_at'] }}</div>
    <div style="margin-top:8px;font-size:11px;color:#94a3b8;">Este documento pode ser impresso ou salvo em PDF através do navegador (Ctrl+P)</div>
  </div>
</div>

</body>
</html>
