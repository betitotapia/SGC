@extends('adminlte::page')

@section('title', 'Agregar Firmante')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Agregar firmante a plantilla</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.approval-templates.index') }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.approval-templates.store') }}">
        @csrf
        @include('quality.approval-templates._form')
        <div class="mt-3">
          <button class="btn btn-primary">Guardar</button>
          <a class="btn btn-outline-secondary" href="{{ route('quality.approval-templates.index') }}">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
@stop
