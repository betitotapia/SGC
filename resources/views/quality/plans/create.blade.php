@extends('adminlte::page')

@section('title', 'Nuevo Plan')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Nuevo plan</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.index') }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.plans.store') }}">
        @csrf
        @include('quality.plans._form')
        <button class="btn btn-primary">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.index') }}">Cancelar</a>
      </form>
    </div>
  </div>
@stop