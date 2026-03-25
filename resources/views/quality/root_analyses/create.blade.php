@extends('adminlte::page')

@section('title', 'Nuevo Análisis de Causa Raíz')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Nuevo análisis de causa raíz del plan {{ $plan->folio }}</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('quality.root-analyses.store', $plan) }}" enctype="multipart/form-data">
            @csrf
            @include('quality.root_analyses._form')

            <button class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
        </form>
    </div>
</div>
@stop