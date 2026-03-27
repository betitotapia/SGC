@extends('adminlte::page')

@section('title', 'Detalle Plan')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h1 class="m-0">Plan: {{ $plan->folio }}</h1>
      <div class="text-muted">
        Status (manual):
        <span class="badge badge-secondary">{{ $plan->status }}</span>
      </div>
    </div>

    <div class="d-flex gap-2">
      @can('quality.kanban.manage')
        <a class="btn btn-outline-primary" href="{{ route('quality.kanban.show', $plan) }}">Tablero</a>
      @endcan

      <a class="btn btn-outline-danger" href="{{ route('quality.plans.pdf', $plan) }}" target="_blank">PDF</a>

      @can('quality.plans.update')
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.edit', $plan) }}">Editar</a>
      @endcan

      <a class="btn btn-outline-dark" href="{{ route('quality.plans.index') }}">Volver</a>
    </div>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">Hallazgo</h5>
          <div class="border rounded p-2 bg-light">{{ $plan->finding }}</div>

          <div class="row mt-3">
            <div class="col-md-3">
              <small class="text-muted">Fecha de apertura</small>
              <div>{{ optional($plan->open_date)->format('Y-m-d') }}</div>
            </div>

            <div class="col-md-3">
              <small class="text-muted">Origen</small>
              <div>{{ $plan->origin }}</div>
            </div>

            <div class="col-md-3">
              <small class="text-muted">Tipo hallazgo</small>
              <div>{{ $plan->finding_type }}</div>
            </div>

            <div class="col-md-3">
              <small class="text-muted">Proceso</small>
              <div>{{ $plan->process }}</div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <small class="text-muted">Detectada por</small>
              <div>{{ $plan->detected_by }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Tipo de auditor</small>
              <div>{{ $plan->auditor_type }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Departamento</small>
              <div>{{ optional($plan->department)->name }}</div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <small class="text-muted">Responsable</small>
              <div>{{ $plan->owner_name }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Email responsable</small>
              <div>{{ $plan->owner_email }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Responsable de soporte</small>
              <div>{{ optional($plan->owner)->name }}</div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4">
              <small class="text-muted">Fecha compromiso</small>
              <div>{{ optional($plan->commitment_date)->format('Y-m-d') }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Fecha cierre</small>
              <div>{{ optional($plan->close_date)->format('Y-m-d') }}</div>
            </div>

            <div class="col-md-4">
              <small class="text-muted">Observaciones</small>
              <div>{{ $plan->notes }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      @php
        $p = (int) ($plan->progress ?? 0);
        $closed = $plan->tasks->where('status', \App\Models\QualityTask::STATUS_CLOSED)->count();
        $total = $plan->tasks->count();

        if ($p >= 100) {
            $barClass = 'bg-success';
            $icon = 'fas fa-check-circle';
            $label = 'Completado';
        } elseif ($p >= 80) {
            $barClass = 'bg-info';
            $icon = 'fas fa-rocket';
            $label = 'Avanzado';
        } elseif ($p >= 40) {
            $barClass = 'bg-warning';
            $icon = 'fas fa-exclamation-triangle';
            $label = 'En riesgo';
        } else {
            $barClass = 'bg-danger';
            $icon = 'fas fa-hourglass-half';
            $label = 'Crítico';
        }

        $isOverdue = false;
        if (!empty($plan->commitment_date) && $p < 100) {
            $isOverdue = \Carbon\Carbon::parse($plan->commitment_date)->isPast();
        }
      @endphp

      <div class="card {{ $isOverdue ? 'card-danger' : 'card-outline card-primary' }}">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <i class="{{ $icon }} mr-2"></i>
            <strong>Progreso</strong>
          </div>

          <span class="badge badge-pill {{ $p >= 100 ? 'badge-success' : ($p >= 80 ? 'badge-info' : ($p >= 40 ? 'badge-warning' : 'badge-danger')) }}"
                style="font-size: 14px; padding: 8px 10px;">
            {{ $p }}%
          </span>
        </div>

        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
              <div class="font-weight-bold">{{ $label }}</div>
              <div class="text-muted small">Cerradas / Total: {{ $closed }} / {{ $total }}</div>
            </div>

            @if($isOverdue)
              <span class="badge badge-danger" style="font-size: 12px;">
                <i class="fas fa-clock mr-1"></i> VENCIDO
              </span>
            @elseif(!empty($plan->commitment_date))
              <span class="badge badge-light" style="font-size: 12px;">
                <i class="far fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::parse($plan->commitment_date)->format('Y-m-d') }}
              </span>
            @endif
          </div>

          <div class="progress" style="height: 18px; border-radius: 10px; overflow: hidden;">
            <div class="progress-bar {{ $barClass }}"
                 role="progressbar"
                 style="width: {{ $p }}%; transition: width .6s ease; font-weight:600;"
                 aria-valuenow="{{ $p }}"
                 aria-valuemin="0"
                 aria-valuemax="100"></div>
          </div>

          <div class="mt-2 small text-muted">
            @if($total === 0)
              Sin tareas aún. Agrega tareas para calcular el avance.
            @elseif($p >= 100)
              <i class="fas fa-check mr-1 text-success"></i> Listo para cierre / validación.
            @elseif($isOverdue)
              <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Requiere atención inmediata.
            @else
              <i class="fas fa-chart-line mr-1"></i> Avance calculado automáticamente por tareas cerradas.
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('quality.plans.partials._tasks', ['plan' => $plan])
  @include('quality.plans.partials._monitorings', ['plan' => $plan])
  @include('quality.plans.partials._root_analyses', ['plan' => $plan])

  @if(!empty($plan->final_result))
    @include('quality.plans.partials._final_result', ['plan' => $plan])
  @endif
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('tasks-sortable');
    if (!tbody) return;

    const refreshNumbers = () => {
        tbody.querySelectorAll('tr[data-id]').forEach((row, index) => {
            const orderCell = row.querySelector('.drag-order-number');
            if (orderCell) {
                orderCell.textContent = index + 1;
            }
        });
    };

    new Sortable(tbody, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'table-warning',
        onEnd: function () {
            refreshNumbers();

            const ids = Array.from(tbody.querySelectorAll('tr[data-id]'))
                .map(row => parseInt(row.dataset.id, 10));

            fetch("{{ route('quality.tasks.reorder', $plan) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ tasks: ids })
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || 'Error al guardar el orden');
                }
                return data;
            })
            .then(data => {
                if (!data.ok) {
                    throw new Error(data.message || 'No se pudo guardar el orden');
                }
            })
            .catch(error => {
                console.error('Error reorder:', error);
                alert('No se pudo guardar el nuevo orden.');
                location.reload();
            });
        }
    });
});
</script>
@stop
