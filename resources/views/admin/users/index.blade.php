@extends('adminlte::page')

@section('title','Usuarios')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
  <h1 class="m-0">Usuarios</h1>
  <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Nuevo</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<form class="row mb-3" method="GET" action="{{ route('admin.users.index') }}">
  <div class="col-md-8">
    <input class="form-control" name="q" value="{{ $q }}" placeholder="Buscar por nombre o correo...">
  </div>
  <div class="col-md-4 d-flex gap-2">
    <button class="btn btn-outline-secondary w-100">Buscar</button>
    <a class="btn btn-outline-dark w-100" href="{{ route('admin.users.index') }}">Limpiar</a>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Departamento</th>
            <th>Rol</th>
            <th>Estatus</th>
            <th class="text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
          <tr>
            <td class="font-weight-bold">{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ optional($u->department)->name }}</td>
            <td>{{ $u->getRoleNames()->first() }}</td>
            <td>
              <span class="badge badge-{{ $u->is_active ? 'success' : 'secondary' }}">
                {{ $u->is_active ? 'ACTIVO' : 'INACTIVO' }}
              </span>
            </td>
            <td class="text-right">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.edit',$u) }}">Editar</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>
@stop