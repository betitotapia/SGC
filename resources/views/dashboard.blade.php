@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<div class="d-flex align-items-center justify-content-between no-print">
    <div class="d-flex align-items-center" style="gap: 10px;">
        <h1 class="m-0">Dashboard</h1>
        <span class="badge badge-{{ $isQuality ? 'primary' : 'secondary' }}" style="font-size: 14px;">
            {{ $isQuality ? 'Vista Global de Calidad' : 'Vista de mi Departamento' }}
        </span>
    </div>

    <div class="d-flex align-items-center" style="gap: 8px;">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="printDashboard()">
            <i class="fas fa-print"></i> Imprimir dashboard
        </button>
    </div>
</div>
@stop

@section('content')
<div id="dashboardArea">
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
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Planes abiertos por área</h3>

                    <div class="no-print">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadPlansByAreaChart()">
                            <i class="fas fa-image"></i> Descargar gráfica
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="plansByAreaChart" style="min-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 5 tareas más urgentes</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Área</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($urgentTasks as $task)
                                    @php
                                        $date = optional($task->commitment_date);
                                        $isOver = $date && $date->isPast();
                                        $isSoon = $date && !$isOver && $date->lte(\Carbon\Carbon::today()->addDays(7));
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $task->title }}</strong>
                                            <div class="small text-muted">{{ $task->status }}</div>
                                        </td>
                                        <td>{{ optional(optional($task->plan)->department)->name }}</td>
                                        <td>
                                            @if($date)
                                                <span class="badge badge-{{ $isOver ? 'danger' : ($isSoon ? 'warning' : 'secondary') }}">
                                                    {{ $date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin fecha</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">Sin tareas pendientes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumen rápido</h3>
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
            <h3 class="card-title">Semáforo por área</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Área</th>
                            <th>Planes abiertos</th>
                            <th>Pendientes</th>
                            <th>Por vencer</th>
                            <th>Vencidas</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areasRisk as $area)
                            @php
                                if ($area->overdue_tasks_count > 0) {
                                    $badge = 'danger';
                                    $label = 'Crítico';
                                } elseif ($area->upcoming_tasks_count > 0) {
                                    $badge = 'warning';
                                    $label = 'Atención';
                                } elseif ($area->pending_tasks_count > 0) {
                                    $badge = 'info';
                                    $label = 'En seguimiento';
                                } else {
                                    $badge = 'success';
                                    $label = 'Controlado';
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $area->name }}</strong></td>
                                <td>{{ $area->open_plans_count }}</td>
                                <td>{{ $area->pending_tasks_count }}</td>
                                <td><span class="badge badge-warning">{{ $area->upcoming_tasks_count }}</span></td>
                                <td><span class="badge badge-danger">{{ $area->overdue_tasks_count }}</span></td>
                                <td><span class="badge badge-{{ $badge }}">{{ $label }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Sin datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    @media print {
        .no-print,
        .main-sidebar,
        .main-header,
        .content-header .breadcrumb,
        .btn,
        .card-tools,
        .navbar,
        .main-footer {
            display: none !important;
        }

        .content-wrapper,
        .content,
        .container-fluid,
        .container {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card,
        .small-box {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }

        body {
            background: #fff !important;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('plansByAreaChart').getContext('2d');

    window.plansByAreaChart = new Chart(ctx, {
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
            animation: {
                onComplete: function () {
                    // asegura que la imagen descargada ya tenga el render terminado
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    function downloadPlansByAreaChart() {
        if (!window.plansByAreaChart) {
            alert('No se encontró la gráfica.');
            return;
        }

        const link = document.createElement('a');
        link.href = window.plansByAreaChart.toBase64Image('image/png', 1);
        link.download = 'planes_abiertos_por_area.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printDashboard() {
        window.print();
    }
</script>
@stop