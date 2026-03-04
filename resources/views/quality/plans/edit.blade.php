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
<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label>Folio *</label>
      <input class="form-control" name="folio" value="{{ old('folio', $plan->folio) }}" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Proceso</label>
      <input class="form-control" name="process" value="{{ old('process', $plan->process) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Tipo de hallazgo</label>
      <input class="form-control" name="finding_type" value="{{ old('finding_type', $plan->finding_type) }}">
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Hallazgo *</label>
      <textarea class="form-control" name="finding" rows="3" required>{{ old('finding', $plan->finding) }}</textarea>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Actividad</label>
      <textarea class="form-control" name="activity" rows="2">{{ old('activity', $plan->activity) }}</textarea>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Causa raíz</label>
      <textarea class="form-control" name="root_cause" rows="2">{{ old('root_cause', $plan->root_cause) }}</textarea>
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Departamento</label>
      <input class="form-control" name="department" value="{{ old('department', $plan->department) }}">
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Responsable (texto)</label>
      <input class="form-control" name="owner_name" value="{{ old('owner_name', $plan->owner_name) }}">
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Responsable (usuario opcional)</label>
      <select class="form-control" name="owner_id">
        <option value="">-- Sin asignar --</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('owner_id', $plan->owner_id) == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Fecha compromiso</label>
      <input type="date" class="form-control" name="commitment_date" value="{{ old('commitment_date', optional($plan->commitment_date)->format('Y-m-d')) }}">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Fecha cierre</label>
      <input type="date" class="form-control" name="close_date" value="{{ old('close_date', optional($plan->close_date)->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Status (manual) *</label>
      <input class="form-control" name="status" value="{{ old('status', $plan->status ?? 'ABIERTO') }}" required>
      <small class="text-muted">Ej: ABIERTO, EN REVISION, VALIDADO, CANCELADO…</small>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Progreso (auto)</label>
      <input class="form-control" value="{{ $plan->progress ?? 0 }}%" disabled>
      <small class="text-muted">Se calcula por tareas cerradas.</small>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Observaciones</label>
      <textarea class="form-control" name="notes" rows="2">{{ old('notes', $plan->notes) }}</textarea>
    </div>
  </div>
</div>

        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-outline-secondary" href="{{ route('quality.plans.show', $plan) }}">Cancelar</a>
      </form>
    </div>
  </div>
@stop
