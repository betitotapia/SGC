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
                        <th style="width: 60px;">Orden</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Compromiso</th>
                        <th>Asignado</th>
                        <th>Evidencias</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody id="tasks-sortable">
                    @forelse($plan->tasks as $task)
                        <tr data-id="{{ $task->id }}">
                           <td class="drag-handle" style="cursor: grab; white-space: nowrap;">
                                <i class="fas fa-grip-vertical text-muted mr-1"></i>
                                <span class="drag-order-number">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <div class="font-weight-bold">{{ $task->title }}</div>

                                @if($task->description)
                                    <small class="text-muted d-block">{{ $task->description }}</small>
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
                                    <div class="text-muted small">
                                        Cerrada: {{ $task->closed_at->format('Y-m-d H:i') }}
                                    </div>
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
                                            <button class="btn btn-outline-primary" type="submit">Subir</button>
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
                                                        <button class="btn btn-link btn-sm text-danger" type="submit">Eliminar</button>
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
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            data-toggle="collapse"
                                            data-target="#reviewBox{{ $task->id }}">
                                        Comentar
                                    </button>

                                    <form method="POST" action="{{ route('quality.tasks.toggle', [$plan, $task]) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" type="submit">
                                            {{ $task->status === \App\Models\QualityTask::STATUS_CLOSED ? 'Reabrir' : 'Cerrar' }}
                                        </button>
                                    </form>

                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('quality.tasks.edit', [$plan, $task]) }}">Editar</a>
                                @endcan

                                @can('quality.tasks.delete')
                                    <form method="POST"
                                          action="{{ route('quality.tasks.destroy', [$plan, $task]) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar tarea?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                @endcan

                                @can('quality.tasks.update')
                                    <div class="collapse mt-2 text-left" id="reviewBox{{ $task->id }}">
                                        <form method="POST" action="{{ route('quality.tasks.review', [$plan, $task]) }}">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <textarea class="form-control form-control-sm"
                                                          name="review_comment"
                                                          rows="2"
                                                          placeholder="Indica por qué no se cierra la tarea y qué falta para completarla..."></textarea>
                                            </div>
                                            <button class="btn btn-sm btn-warning" type="submit">Guardar comentario</button>
                                        </form>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Aún no hay tareas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
