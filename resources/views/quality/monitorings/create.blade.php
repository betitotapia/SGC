@extends('adminlte::page')

@section('title', 'Nuevo Monitoreo')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Nuevo monitoreo del plan {{ $plan->folio }}</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('quality.monitorings.store', $plan) }}">
            @csrf
            @include('quality.monitorings._form')

            <button class="btn btn-primary">Guardar</button>
            <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
        </form>
    </div>
</div>
@stop
