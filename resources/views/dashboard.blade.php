@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="m-0">Dashboard</h1>
@stop

@section('content')

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalPlans }}</h3>
                <p>Planes totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $openPlans }}</h3>
                <p>Planes abiertos</p>
            </div>
            <div class="icon">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $closedPlans }}</h3>
                <p>Planes cerrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $overdueTasks }}</h3>
                <p>Tareas vencidas</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Planes abiertos por área</h3>
            </div>
            <div class="card-body">
                <canvas id="plansByAreaChart" style="min-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resumen de tareas</h3>
            </div>
            <div class="card-body">
                <p><strong>Tareas por vencer (7 días):</strong> {{ $upcomingTasks }}</p>
                <p><strong>Tareas vencidas:</strong> {{ $overdueTasks }}</p>
                <p><strong>Planes abiertos:</strong> {{ $openPlans }}</p>
                <p><strong>Planes cerrados:</strong> {{ $closedPlans }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Áreas con tareas pendientes / por vencer / vencidas</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Área</th>
                        <th>Planes abiertos</th>
                        <th>Tareas pendientes</th>
                        <th>Por vencer</th>
                        <th>Vencidas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areasRisk as $area)
                        <tr>
                            <td><strong>{{ $area->name }}</strong></td>
                            <td>{{ $area->open_plans_count }}</td>
                            <td>{{ $area->pending_tasks_count }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    {{ $area->upcoming_tasks_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-danger">
                                    {{ $area->overdue_tasks_count }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Sin datos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('plansByAreaChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Planes abiertos',
                data: @json($chartValues),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
@stop