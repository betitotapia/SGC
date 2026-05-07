@extends('adminlte::page')

@section('title', 'Control Documental')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Control Documental</h1>
    @can('documents.create')
      <a class="btn btn-primary" href="{{ route('quality.documents.create') }}">Nuevo documento</a>
    @endcan
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  {{-- Filtros --}}
  <form class="row mb-3" method="GET" action="{{ route('quality.documents.index') }}">
    <div class="col-md-5">
      <input class="form-control" name="q" value="{{ $q }}"
             placeholder="Buscar por folio, título o departamento...">
    </div>
    <div class="col-md-3">
      <select class="form-control" name="type">
        <option value="">Todos los tipos</option>
        @foreach(\App\Models\Document::TYPE_LABELS as $value => $label)
          <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-outline-secondary w-100" type="submit">Buscar</button>
      <a class="btn btn-outline-dark w-100" href="{{ route('quality.documents.index') }}">Limpiar</a>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Título</th>
              <th>Tipo</th>
              <th>Departamento</th>
              <th>Versión</th>
              <th>Estado</th>
              <th class="text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($documents as $document)
              <tr>
                <td class="font-weight-bold">{{ $document->folio }}</td>
                <td>{{ $document->title }}</td>
                <td>
                  @php
                    $typeColors = [
                      'procedure'        => 'primary',
                      'process'          => 'info',
                      'format'           => 'secondary',
                      'work_instruction' => 'warning',
                    ];
                  @endphp
                  <span class="badge badge-{{ $typeColors[$document->type] ?? 'secondary' }}">
                    {{ $document->typeLabel() }}
                  </span>
                </td>
                <td>{{ optional($document->department)->name }}</td>
                <td>
                  @if($document->currentVersion)
                    <span class="badge badge-light border">v{{ $document->currentVersion->version_number }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @php
                    $statusColors = [
                      'draft'       => 'secondary',
                      'in_review'   => 'warning',
                      'in_approval' => 'info',
                      'published'   => 'success',
                      'obsolete'    => 'dark',
                    ];
                  @endphp
                  <span class="badge badge-{{ $statusColors[$document->status] ?? 'secondary' }}">
                    {{ $document->statusLabel() }}
                  </span>
                </td>
                <td class="text-right">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('quality.documents.show', $document) }}">Ver</a>
                  @can('documents.update')
                    @if($document->isEditable())
                      <a class="btn btn-sm btn-outline-secondary"
                         href="{{ route('quality.documents.edit', $document) }}">Editar</a>
                    @endif
                  @endcan
                  @can('documents.delete')
                    @if($document->isEditable())
                      <form class="d-inline" method="POST"
                            action="{{ route('quality.documents.destroy', $document) }}"
                            onsubmit="return confirm('¿Eliminar documento {{ $document->folio }}?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                      </form>
                    @endif
                  @endcan
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Sin documentos registrados</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $documents->links() }}
  </div>
@stop
