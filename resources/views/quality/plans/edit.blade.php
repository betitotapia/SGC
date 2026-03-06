@extends('adminlte::page')

@section('title', 'Editar Plan')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Editar plan: {{ $plan->folio }}</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.plans.update', $plan) }}">
        @csrf
        @method('PUT')
        @include('quality.plans._form')
        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
      </form>
    </div>
  </div>
@stop