@extends('layouts.app')

@section('title', 'Métricas do Painel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Métricas Avançadas</h1>
        </div>
    </div>
    
    <!-- Gráficos de performance -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Tickets por Dia (Últimos 7 dias)</h5>
                </div>
                <div class="card-body">
                    <canvas id="ticketsPerDayChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Tempo Médio de Resolução</h5>
                </div>
                <div class="card-body">
                    <canvas id="resolutionTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SLA e Alertas -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Alertas de SLA</h5>
                </div>
                <div class="card-body">
                    <div id="sla-alerts"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Implementar gráficos com Chart.js
const ctx = document.getElementById('ticketsPerDayChart').getContext('2d');
const ticketsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
        datasets: [{
            label: 'Tickets Criados',
            data: [12, 19, 3, 5, 2, 3, 7],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});
</script>
@endsection
