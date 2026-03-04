@extends('adminlte::page')

@section('title','Nuevo Usuario')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">Nuevo Usuario</h1>
  <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Volver</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Nombre *</label>
            <input class="form-control" name="name" value="{{ old('name',$user->name) }}" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>Email *</label>
            <input class="form-control" name="email" type="email" value="{{ old('email',$user->email) }}" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>Password *</label>
            <input class="form-control" name="password" type="password" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>Departamento</label>
            <select class="form-control" name="department_id">
              <option value="">-- Selecciona --</option>
              @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>Rol *</label>
            <select class="form-control" name="role" required>
              <option value="">-- Selecciona --</option>
              @foreach($roles as $r)
                <option value="{{ $r->name }}" @selected(old('role')==$r->name)>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6 d-flex align-items-center">
          <div class="form-group form-check mt-4">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Activo</label>
          </div>
        </div>
      </div>

      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancelar</a>
    </form>
  </div>
</div>
@stop
