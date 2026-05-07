@extends('adminlte::page')

@section('title', 'Editar Documento')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Editar — <span class="text-muted">{{ $document->folio }}</span></h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.documents.show', $document) }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.documents.update', $document) }}">
        @csrf
        @method('PUT')
        @include('quality.documents._form')
        <div class="mt-3">
          <button class="btn btn-primary">Actualizar</button>
          <a class="btn btn-outline-secondary" href="{{ route('quality.documents.show', $document) }}">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
@stop
