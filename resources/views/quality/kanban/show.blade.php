@extends('adminlte::page')

@section('title', 'Tablero')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h1 class="m-0">Tablero Kanban - Plan {{ $plan->folio }}</h1>
      <div class="text-muted">Arrastra tarjetas entre columnas para cambiar status.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <style>
    .kanban-board{display:flex;gap:12px;overflow:auto;padding-bottom:12px}
    .kanban-col{min-width:320px;flex:0 0 320px}
    .kanban-drop{min-height:240px;border:1px dashed #c9c9c9;border-radius:8px;padding:10px;background:#fafafa}
    .kanban-card{background:#fff;border:1px solid #e6e6e6;border-radius:10px;padding:10px;margin-bottom:10px;cursor:grab}
    .kanban-card.dragging{opacity:.5}
    .kanban-meta{font-size:12px;color:#6c757d}
  </style>

  <div class="kanban-board">
    @foreach($columns as $status => $tasks)
      <div class="kanban-col">
        <div class="card">
          <div class="card-header">
            <strong>{{ $status }}</strong>
            <span class="badge badge-secondary float-right">{{ $tasks->count() }}</span>
          </div>
          <div class="card-body">
            <div class="kanban-drop" data-status="{{ $status }}">
              @foreach($tasks as $t)
                <div class="kanban-card" draggable="true" data-task-id="{{ $t->id }}">
                  <div class="font-weight-bold">{{ $t->title }}</div>
                  @if($t->assignee)
                    <div class="kanban-meta">Asignado: {{ $t->assignee->name }}</div>
                  @endif
                  @if($t->commitment_date)
                    <div class="kanban-meta">Compromiso: {{ $t->commitment_date->format('Y-m-d') }}</div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <script>
    const csrf = "{{ csrf_token() }}";
    const postUrl = "{{ route('quality.kanban.status', $plan) }}";

    let dragged = null;

    document.querySelectorAll('.kanban-card').forEach(card => {
      card.addEventListener('dragstart', e => {
        dragged = card;
        card.classList.add('dragging');
        e.dataTransfer.setData('text/plain', card.dataset.taskId);
      });
      card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
      });
    });

    document.querySelectorAll('.kanban-drop').forEach(drop => {
      drop.addEventListener('dragover', e => e.preventDefault());
      drop.addEventListener('drop', async e => {
        e.preventDefault();
        const taskId = e.dataTransfer.getData('text/plain');
        const newStatus = drop.dataset.status;

        // mover UI primero
        const card = document.querySelector(`.kanban-card[data-task-id="${taskId}"]`);
        if (card) drop.prepend(card);

        // persistir por AJAX
        try {
          const res = await fetch(postUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ task_id: parseInt(taskId), status: newStatus })
          });

          if (!res.ok) {
            alert('No se pudo actualizar el status. Revisa permisos o sesión.');
          }
        } catch (err) {
          console.error(err);
          alert('Error de red al actualizar status.');
        }
      });
    });
  </script>
@stop
