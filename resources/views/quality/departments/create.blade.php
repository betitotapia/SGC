@extends('adminlte::page')

@section('title', 'Nuevo Departamento')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">Nuevo Departamento</h1>
  <a class="btn btn-outline-secondary" href="{{ route('quality.departments.index') }}">Volver</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('quality.departments.store') }}">
      @csrf

      <div class="form-group">
        <label>Nombre *</label>
        <input class="form-control" name="name" value="{{ old('name', $department->name) }}" required>
      </div>

      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
               @checked(old('is_active', $department->is_active))>
        <label class="form-check-label" for="is_active">Activo</label>
      </div>

      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="{{ route('quality.departments.index') }}">Cancelar</a>
    </form>
  </div>
</div>
@stop