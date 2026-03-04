@extends('adminlte::page')

@section('title', 'Departamentos')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">Departamentos</h1>
  <a class="btn btn-primary" href="{{ route('quality.departments.create') }}">Nuevo</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<form class="row mb-3" method="GET" action="{{ route('quality.departments.index') }}">
  <div class="col-md-8">
    <input class="form-control" name="q" value="{{ $q }}" placeholder="Buscar por nombre...">
  </div>
  <div class="col-md-4 d-flex gap-2">
    <button class="btn btn-outline-secondary w-100" type="submit">Buscar</button>
    <a class="btn btn-outline-dark w-100" href="{{ route('quality.departments.index') }}">Limpiar</a>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Estatus</th>
            <th class="text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($departments as $d)
            <tr>
              <td class="font-weight-bold">{{ $d->name }}</td>
              <td>
                <span class="badge badge-{{ $d->is_active ? 'success' : 'secondary' }}">
                  {{ $d->is_active ? 'ACTIVO' : 'INACTIVO' }}
                </span>
              </td>
              <td class="text-right">
                <form method="POST" action="{{ route('quality.departments.toggle', $d) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary">
                    {{ $d->is_active ? 'Desactivar' : 'Activar' }}
                  </button>
                </form>

                <a class="btn btn-sm btn-outline-secondary" href="{{ route('quality.departments.edit', $d) }}">Editar</a>

                <form method="POST" action="{{ route('quality.departments.destroy', $d) }}" class="d-inline"
                      onsubmit="return confirm('¿Eliminar departamento?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center py-4">Sin registros</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="mt-3">
  {{ $departments->links() }}
</div>
@stop