@extends('adminlte::page')

@section('title', 'Plantillas de Firmas')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Plantillas de firmas</h1>
    <a class="btn btn-primary" href="{{ route('quality.approval-templates.create') }}">
      <i class="fas fa-plus mr-1"></i>Agregar firmante
    </a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  {{-- Filtro por departamento --}}
  <form class="row mb-3" method="GET" action="{{ route('quality.approval-templates.index') }}">
    <div class="col-md-5">
      <select class="form-control" name="department_id">
        <option value="">Todos los departamentos</option>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected($departmentId == $d->id)>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-outline-secondary w-100" type="submit">Filtrar</button>
      <a class="btn btn-outline-dark w-100" href="{{ route('quality.approval-templates.index') }}">Limpiar</a>
    </div>
  </form>

  {{-- Tablas agrupadas por departamento --}}
  @forelse($templates as $deptId => $rows)
    @php $dept = $rows->first()->department; @endphp
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span class="font-weight-bold">
          <i class="fas fa-building mr-1 text-muted"></i>
          {{ optional($dept)->name ?? 'Departamento #' . $deptId }}
        </span>
        <a class="btn btn-sm btn-outline-primary"
           href="{{ route('quality.approval-templates.create', ['department_id' => $deptId]) }}">
          <i class="fas fa-plus mr-1"></i>Agregar firmante
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th style="width:40px;">#</th>
                <th>Tipo de documento</th>
                <th>Rol</th>
                <th>Firmante</th>
                <th class="text-right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rows as $template)
                <tr>
                  <td>
                    <span class="badge badge-secondary">{{ $template->order }}</span>
                  </td>
                  <td>
                    @if($template->document_type)
                      @php
                        $typeColors = [
                          'procedure'        => 'primary',
                          'process'          => 'info',
                          'format'           => 'secondary',
                          'work_instruction' => 'warning',
                        ];
                      @endphp
                      <span class="badge badge-{{ $typeColors[$template->document_type] ?? 'secondary' }}">
                        {{ \App\Models\Document::TYPE_LABELS[$template->document_type] ?? $template->document_type }}
                      </span>
                    @else
                      <span class="text-muted"><em>Todos los tipos</em></span>
                    @endif
                  </td>
                  <td>
                    @php
                      $roleColors = ['author' => 'secondary', 'reviewer' => 'warning', 'approver' => 'success'];
                    @endphp
                    <span class="badge badge-{{ $roleColors[$template->role_in_approval] ?? 'secondary' }}">
                      {{ \App\Models\DocumentApproval::ROLE_LABELS[$template->role_in_approval] ?? $template->role_in_approval }}
                    </span>
                  </td>
                  <td>
                    <strong>{{ optional($template->user)->name ?? '—' }}</strong>
                    @if($template->user)
                      <br><small class="text-muted">{{ $template->user->email }}</small>
                    @endif
                  </td>
                  <td class="text-right">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('quality.approval-templates.edit', $template) }}">
                      Editar
                    </a>
                    <form class="d-inline" method="POST"
                          action="{{ route('quality.approval-templates.destroy', $template) }}"
                          onsubmit="return confirm('¿Eliminar este firmante de la plantilla?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @empty
    <div class="card">
      <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-pen-nib fa-2x mb-2"></i>
        <p class="mb-0">No hay plantillas de firmas configuradas.</p>
        <a class="btn btn-primary mt-3" href="{{ route('quality.approval-templates.create') }}">
          Agregar primer firmante
        </a>
      </div>
    </div>
  @endforelse

  {{-- Nota explicativa --}}
  <div class="alert alert-info mt-2">
    <i class="fas fa-info-circle mr-1"></i>
    <strong>¿Cómo funciona?</strong>
    Estas plantillas definen quién debe firmar cada documento al enviarlo a revisión.
    El <strong>orden</strong> determina la secuencia de firmas (1 = primero).
    Puedes definir firmantes por tipo específico de documento o para <em>todos los tipos</em> de un departamento.
    Las plantillas específicas por tipo tienen prioridad sobre las genéricas.
  </div>
@stop
