@extends('adminlte::page')

@section('title', 'Editar Tarea')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Editar tarea (Plan {{ $plan->folio }})</h1>
    <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Volver</a>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('quality.tasks.update', [$plan, $task]) }}">
        @csrf
        @method('PUT')
<div class="row">
  <div class="col-md-8">
    <div class="form-group">
      <label>Título *</label>
      <input class="form-control" name="title" value="{{ old('title', $task->title) }}" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Status *</label>
      <select class="form-control" name="status" required>
        @php
          $opts = [
            \App\Models\QualityTask::STATUS_OPEN => 'ABIERTA',
            \App\Models\QualityTask::STATUS_IN_PROGRESS => 'EN PROCESO',
            \App\Models\QualityTask::STATUS_CLOSED => 'CERRADA',
          ];
          $sel = old('status', $task->status ?? \App\Models\QualityTask::STATUS_OPEN);
        @endphp
        @foreach($opts as $val => $label)
          <option value="{{ $val }}" @selected($sel === $val)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Descripción</label>
      <textarea class="form-control" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Fecha compromiso</label>
      <input type="date" class="form-control" name="commitment_date" value="{{ old('commitment_date', optional($task->commitment_date)->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="col-md-8">
    <div class="form-group">
      <label>Asignado (usuario)</label>
      <select class="form-control" name="assignee_id">
        <option value="">-- Sin asignar --</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('assignee_id', $task->assignee_id) == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
      </select>
    </div>
  </div>
</div>
<div class="col-12">
    <div class="form-group">
        <label>Comentarios de la tarea</label>
        <textarea class="form-control" name="comments" rows="3">{{ old('comments', $task->comments) }}</textarea>
        <small class="text-muted">Comentarios generales del responsable/colaborador sobre la tarea.</small>
    </div>
</div>

        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
      </form>
    </div>
  </div>
@stop
