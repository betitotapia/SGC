@extends('adminlte::page')

@section('title', 'Editar Firmante')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Editar firmante</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.approval-templates.index') }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.approval-templates.update', $approvalTemplate) }}">
        @csrf
        @method('PUT')
        @include('quality.approval-templates._form')
        <div class="mt-3">
          <button class="btn btn-primary">Actualizar</button>
          <a class="btn btn-outline-secondary"
             href="{{ route('quality.approval-templates.index', ['department_id' => $approvalTemplate->department_id]) }}">
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
@stop
