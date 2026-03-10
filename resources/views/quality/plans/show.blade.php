@extends('adminlte::page')

@section('title', 'Detalle Plan')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h1 class="m-0">Plan: {{ $plan->folio }}</h1>
      <div class="text-muted">Status (manual): <span class="badge badge-secondary">{{ $plan->status }}</span></div>
    </div>
    <div class="d-flex gap-2">
      @can('quality.kanban.manage')
        <a class="btn btn-outline-primary" href="{{ route('quality.kanban.show', $plan) }}">Tablero</a>
      @endcan
      @can('quality.plans.update')
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.edit', $plan) }}">Editar</a>
      @endcan
      
      <a class="btn btn-outline-danger" href="{{ route('quality.plans.pdf', $plan) }}" target="_blank"> PDF</a>
      
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
            <div class="col-md-3"><small class="text-muted">Proceso</small><div>{{ $plan->process }}</div></div>
            <div class="col-md-3"><small class="text-muted">Tipo hallazgo</small><div>{{ $plan->finding_type }}</div></div>
            <div class="col-md-3"><small class="text-muted">Departamento</small><div>{{ optional($plan->department)->name }}</div></div>
            <div class="col-md-3"><small class="text-muted">Responsable</small><div>{{ $plan->owner_name }}</div></div>
          </div>

          <div class="row mt-3">
            <div class="col-md-3"><small class="text-muted">Fecha compromiso</small><div>{{ optional($plan->commitment_date)->format('Y-m-d') }}</div></div>
            <div class="col-md-3"><small class="text-muted">Fecha cierre</small><div>{{ optional($plan->close_date)->format('Y-m-d') }}</div></div>
            <div class="col-md-6"><small class="text-muted">Notas</small><div>{{ $plan->notes }}</div></div>
          </div>
        </div>
      </div>
    </div>
        <div class="col-md-4">
    @php
        $p = (int) ($plan->progress ?? 0);
        $closed = $plan->tasks->where('status', \App\Models\QualityTask::STATUS_CLOSED)->count();
        $total  = $plan->tasks->count();

        // Semáforo
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

        // Vencido (si hay fecha compromiso y no está completado)
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
                @else
                    @if(!empty($plan->commitment_date))
                        <span class="badge badge-light" style="font-size: 12px;">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ \Carbon\Carbon::parse($plan->commitment_date)->format('Y-m-d') }}
                        </span>
                    @endif
                @endif
            </div>

            <div class="progress" style="height: 18px; border-radius: 10px; overflow: hidden;">
                <div class="progress-bar {{ $barClass }}"
                     role="progressbar"
                     style="width: {{ $p }}%; transition: width .6s ease; font-weight:600;"
                     aria-valuenow="{{ $p }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>

            <div class="mt-2 small text-muted">
                @if($total === 0)
                    Sin tareas aún. Agrega tareas para calcular el avance.
                @else
                    @if($p >= 100)
                        <i class="fas fa-check mr-1 text-success"></i> Listo para cierre / validación.
                    @elseif($isOverdue)
                        <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Requiere atención inmediata (fecha vencida).
                    @else
                        <i class="fas fa-chart-line mr-1"></i> Avance calculado automáticamente por tareas cerradas.
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
    
    </div>
  </div>

  <div class="d-flex align-items-center justify-content-between my-2">
    <h4 class="m-0">Tareas</h4>
    @can('quality.tasks.create')
  <a class="btn btn-primary btn-sm" href="{{ route('quality.tasks.create', $plan) }}">Nueva tarea</a>
@endcan
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Título</th>
              <th>Status</th>
              <th>Compromiso</th>
              <th>Asignado</th>
              <th>Evidencias</th>
              <th class="text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plan->tasks as $task)
              <tr>
                <td>
                  <div class="font-weight-bold">{{ $task->title }}</div>
                  @if($task->description)
                    <small class="text-muted">{{ $task->description }}</small>
                  @endif
                  @if($task->comments)
                      <div class="small mt-1">
                          <strong>Comentarios:</strong> {{ $task->comments }}
                      </div>
                  @endif

                  @if($task->review_comment)
                      <div class="small mt-1 text-danger">
                          <strong>Comentario de revisión:</strong> {{ $task->review_comment }}
                          @if($task->reviewer)
                              <div class="text-muted">
                                  Revisado por {{ $task->reviewer->name }}
                                  @if($task->reviewed_at)
                                      el {{ $task->reviewed_at->format('Y-m-d H:i') }}
                                  @endif
                              </div>
                          @endif
                      </div>
                  @endif
                                  </td>
                <td>
                  <span class="badge badge-{{ $task->status === \App\Models\QualityTask::STATUS_CLOSED ? 'success' : 'warning' }}">
                    {{ $task->status }}
                  </span>
                  @if($task->closed_at)
                    <div class="text-muted small">Cerrada: {{ $task->closed_at->format('Y-m-d H:i') }}</div>
                  @endif
                </td>
                <td>{{ optional($task->commitment_date)->format('Y-m-d') }}</td>
                <td>{{ optional($task->assignee)->name }}</td>
                <td style="min-width: 280px;">
                  @can('quality.evidences.create')
                <form method="POST" action="{{ route('quality.tasks.evidences.store', $task) }}" enctype="multipart/form-data" class="mb-2">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input class="form-control" type="file" name="file" required>
                        <button class="btn btn-outline-primary">Subir</button>
                    </div>
                </form>
                @endcan

                  @if($task->evidences->count())
                    <div class="small">
                      @foreach($task->evidences as $e)
                        <div class="d-flex align-items-center justify-content-between">
                          <a href="{{ asset('storage/'.$e->path) }}" target="_blank">{{ $e->original_name }}</a>
                         @can('quality.evidences.delete')
                          <form method="POST" action="{{ route('quality.evidences.destroy', $e) }}" onsubmit="return confirm('¿Eliminar evidencia?');">
                              @csrf
                              @method('DELETE')
                              <button class="btn btn-link btn-sm text-danger">Eliminar</button>
                          </form>
                          @endcan
                        </div>
                      @endforeach
                    </div>
                  @else
                    <small class="text-muted">Sin evidencias</small>
                  @endif
                </td>
                <td class="text-right">
                  @can('quality.tasks.update')
                    <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="collapse" data-target="#reviewBox{{ $task->id }}">
                        Comentar
                    </button>
                @endcan
                  @can('quality.tasks.update')
                  <div class="collapse mt-2" id="reviewBox{{ $task->id }}">
                      <form method="POST" action="{{ route('quality.tasks.review', [$plan, $task]) }}">
                          @csrf
                          <div class="form-group mb-2">
                              <textarea class="form-control form-control-sm" name="review_comment" rows="2"
                                        placeholder="Indica por qué no se cierra la tarea y qué falta para completarla..."></textarea>
                          </div>
                          <button class="btn btn-sm btn-warning">Guardar comentario</button>
                      </form>
                  </div>
                  @endcan
                    @can('quality.tasks.update')
                      <form method="POST" action="{{ route('quality.tasks.toggle', [$plan, $task]) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-success">
                          {{ $task->status === \App\Models\QualityTask::STATUS_CLOSED ? 'Reabrir' : 'Cerrar' }}
                        </button>
                      </form>

                      <a class="btn btn-sm btn-outline-secondary" href="{{ route('quality.tasks.edit', [$plan, $task]) }}">Editar</a>
                    @endcan

                    @can('quality.tasks.delete')
                      <form method="POST" action="{{ route('quality.tasks.destroy', [$plan, $task]) }}" class="d-inline"
                            onsubmit="return confirm('¿Eliminar tarea?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                      </form>
                    @endcan
                  </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4">Aún no hay tareas</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@stop
