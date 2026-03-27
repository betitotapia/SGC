@extends('adminlte::page')

@section('title', 'Editar Análisis de Causa Raíz')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Editar análisis de causa raíz del plan {{ $plan->folio }}</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
</div>
@stop

@section('content')
@include('quality._partials.flash')

<div class="card">
    <div class="card-body">
        <form method="POST"
              action="{{ route('quality.root-analyses.update', [$plan, $analysis]) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('quality.root_analyses._form', ['analysis' => $analysis, 'plan' => $plan])

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
        </form>
    </div>
</div>

@if($analysis->files->count())
<div class="card mt-3">
    <div class="card-body">
        <h5 class="mb-3">Archivos cargados</h5>

        <ul class="list-group">
            @foreach($analysis->files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ asset('storage/'.$file->path) }}" target="_blank">
                        {{ $file->original_name }}
                    </a>

                    <form method="POST"
                          action="{{ route('quality.root-analyses.files.destroy', [$plan, $analysis, $file]) }}"
                          onsubmit="return confirm('¿Eliminar archivo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@stop