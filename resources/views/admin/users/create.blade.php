@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

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
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Departamento</label>
                        <select name="department_id" class="form-control">
                            <option value="">-- Selecciona --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    @selected(old('department_id') == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rol *</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Selecciona --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    @selected(old('role') == $role->name)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-group form-check mt-4">
                        <input type="checkbox" class="form-check-input"
                               id="is_active" name="is_active" value="1"
                               @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </div>
</div>
@stop