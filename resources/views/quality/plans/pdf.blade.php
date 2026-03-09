<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Plan de Acción {{ $plan->folio }}</title>
    <style>
        @page {
            margin: 120px 30px 40px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
        }

        footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 1px solid #000;
            vertical-align: middle;
            padding: 6px;
        }

        .logo-cell {
            width: 22%;
            text-align: center;
        }

        .title-cell {
            width: 53%;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }

        .meta-cell {
            width: 25%;
            font-size: 10px;
        }

        .logo {
            max-height: 55px;
            max-width: 140px;
        }

        .meta-line {
            margin-bottom: 4px;
        }

        .section-title {
            background: #e9ecef;
            border: 1px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .table th {
            background: #f3f3f3;
            text-align: left;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: 9px;
            color: #555;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #333;
            border-radius: 2px;
            font-size: 9px;
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        .mt-8 {
            margin-top: 8px;
        }

        .muted {
            color: #666;
        }
    </style>
</head>
<body>

<header>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @php
                    $logoPath = public_path('img/logo-sgc.png');
                @endphp

                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" class="logo">
                @else
                    <div style="font-size: 11px; font-weight: bold;">LOGOTIPO</div>
                @endif
            </td>

            <td class="title-cell">
                PLAN DE ACCIÓN
            </td>

            <td class="meta-cell">
                <div class="meta-line"><strong>CLAVE:</strong> F-SMAM-14</div>
                <div class="meta-line"><strong>REV.:</strong> F</div>
                <div class="meta-line"><strong>FECHA:</strong> 24/06/2025</div>
            </td>
        </tr>
    </table>
</header>

<footer>
    <div class="page-number"></div>
</footer>

<main>
    <div class="section-title">Datos generales del plan</div>
    <table class="table">
        <tr>
            <th style="width: 18%;">Folio</th>
            <td style="width: 32%;">{{ $plan->folio }}</td>

            <th style="width: 18%;">Status</th>
            <td style="width: 32%;">{{ $plan->status }}</td>
        </tr>
        <tr>
            <th>Fecha de apertura</th>
            <td>{{ optional($plan->open_date)->format('d/m/Y') }}</td>

            <th>Fecha compromiso</th>
            <td>{{ optional($plan->commitment_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Fecha cierre</th>
            <td>{{ optional($plan->close_date)->format('d/m/Y') }}</td>

            <th>Progreso</th>
            <td>{{ $plan->progress }}%</td>
        </tr>
        <tr>
            <th>Origen</th>
            <td>{{ $plan->origin }}</td>

            <th>Tipo de hallazgo</th>
            <td>{{ $plan->finding_type }}</td>
        </tr>
        <tr>
            <th>Detectada por</th>
            <td>{{ $plan->detected_by }}</td>

            <th>Tipo de auditor</th>
            <td>{{ $plan->auditor_type }}</td>
        </tr>
        <tr>
            <th>Departamento</th>
            <td>{{ optional($plan->department)->name }}</td>

            <th>Responsable</th>
            <td>{{ $plan->owner_name }}</td>
        </tr>
        <tr>
            <th>Responsable soporte</th>
            <td>{{ optional($plan->owner)->name }}</td>

            <th>Proceso</th>
            <td>{{ $plan->process }}</td>
        </tr>
    </table>

    <div class="section-title">Hallazgo</div>
    <table class="table">
        <tr>
            <td>{{ $plan->finding }}</td>
        </tr>
    </table>

    <div class="section-title">Actividad</div>
    <table class="table">
        <tr>
            <td>{{ $plan->activity }}</td>
        </tr>
    </table>

    <div class="section-title">Causa raíz</div>
    <table class="table">
        <tr>
            <td>{{ $plan->root_cause }}</td>
        </tr>
    </table>

    <div class="section-title">Observaciones</div>
    <table class="table">
        <tr>
            <td>{{ $plan->notes }}</td>
        </tr>
    </table>

    <div class="section-title">Tareas del plan</div>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 18%;">Título</th>
                <th style="width: 22%;">Descripción</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Compromiso</th>
                <th style="width: 14%;">Asignado</th>
                <th style="width: 26%;">Evidencias</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plan->tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>
                        {{ $task->status }}
                        @if($task->closed_at)
                            <div class="small">Cerrada: {{ $task->closed_at->format('d/m/Y H:i') }}</div>
                        @endif
                    </td>
                    <td>{{ optional($task->commitment_date)->format('d/m/Y') }}</td>
                    <td>{{ optional($task->assignee)->name }}</td>
                    <td>
                        @if($task->evidences->count())
                            @foreach($task->evidences as $e)
                                <div>{{ $e->original_name }}</div>
                            @endforeach
                        @else
                            <span class="small">Sin evidencias</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Sin tareas registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</main>

</body>
</html>