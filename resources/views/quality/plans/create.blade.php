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

<div class="row">

{{-- Folio --}}
<div class="col-md-3">
<div class="form-group">
<label>Folio *</label>
<input class="form-control" name="folio" value="{{ old('folio', $plan->folio) }}" required>
</div>
</div>

{{-- Fecha apertura --}}
<div class="col-md-3">
<div class="form-group">
<label>Fecha de apertura</label>
<input type="date" class="form-control" name="open_date"
value="{{ old('open_date', optional($plan->open_date)->format('Y-m-d')) }}">
</div>
</div>

{{-- Origen --}}
<div class="col-md-3">
<div class="form-group">
<label>Origen *</label>
<select class="form-control" name="origin" required>
<option value="">Seleccione</option>
<option value="Matríz de RYO" @selected(old('origin',$plan->origin)=="Matríz de RYO")>Matríz de RYO</option>
<option value="Auditoria" @selected(old('origin',$plan->origin)=="Auditoria")>Auditoria</option>
<option value="Funcionamiento del SGC" @selected(old('origin',$plan->origin)=="Funcionamiento del SGC")>Funcionamiento del SGC</option>
</select>
</div>
</div>

{{-- Tipo Hallazgo --}}
<div class="col-md-3">
<div class="form-group">
<label>Tipo de hallazgo *</label>
<select class="form-control" name="finding_type" required>
<option value="">Seleccione</option>
<option value="Riesgos" @selected(old('finding_type',$plan->finding_type)=="Riesgos")>Riesgos</option>
<option value="Oportunidades" @selected(old('finding_type',$plan->finding_type)=="Oportunidades")>Oportunidades</option>
<option value="Observación" @selected(old('finding_type',$plan->finding_type)=="Observación")>Observación</option>
<option value="No conformidad" @selected(old('finding_type',$plan->finding_type)=="No conformidad")>No conformidad</option>
</select>
</div>
</div>

{{-- Detectada por --}}
<div class="col-md-4">
<div class="form-group">
<label>Detectada por</label>
<input class="form-control" name="detected_by"
value="{{ old('detected_by', $plan->detected_by) }}">
</div>
</div>

{{-- Tipo auditor --}}
<div class="col-md-4">
<div class="form-group">
<label>Tipo de auditor</label>
<select class="form-control" name="auditor_type">
<option value="">Seleccione</option>
<option value="INTERNO" @selected(old('auditor_type',$plan->auditor_type)=="INTERNO")>INTERNO</option>
<option value="EXTERNO" @selected(old('auditor_type',$plan->auditor_type)=="EXTERNO")>EXTERNO</option>
</select>
</div>
</div>

{{-- Proceso --}}
<div class="col-md-4">
<div class="form-group">
<label>Proceso</label>
<input class="form-control" name="process"
value="{{ old('process', $plan->process) }}">
</div>
</div>

{{-- Hallazgo --}}
<div class="col-12">
<div class="form-group">
<label>Hallazgo *</label>
<textarea class="form-control" name="finding" rows="3" required>{{ old('finding', $plan->finding) }}</textarea>
</div>
</div>

{{-- Actividad --}}
<div class="col-12">
<div class="form-group">
<label>Actividad</label>
<textarea class="form-control" name="activity" rows="2">{{ old('activity', $plan->activity) }}</textarea>
</div>
</div>

{{-- Causa raíz --}}
<div class="col-12">
<div class="form-group">
<label>Causa raíz</label>
<textarea class="form-control" name="root_cause" rows="2">{{ old('root_cause', $plan->root_cause) }}</textarea>
</div>
</div>

{{-- Departamento --}}
<div class="col-md-4">
<div class="form-group">
<label>Departamento</label>
<select class="form-control" name="department_id">
<option value="">Seleccione</option>
@foreach($departments as $d)
<option value="{{ $d->id }}"
@selected(old('department_id',$plan->department_id)==$d->id)>
{{ $d->name }}
</option>
@endforeach
</select>
</div>
</div>

{{-- Responsable texto --}}
<div class="col-md-4">
<div class="form-group">
<label>Responsable (propietario)</label>
<input class="form-control" name="owner_name"
value="{{ old('owner_name', $plan->owner_name) }}">
</div>
</div>

{{-- Responsable usuario --}}
<div class="col-md-4">
<div class="form-group">
<label>Responsable de soporte</label>
<select class="form-control" name="owner_id">
<option value="">-- Sin asignar --</option>
@foreach($users as $u)
<option value="{{ $u->id }}"
@selected(old('owner_id',$plan->owner_id)==$u->id)>
{{ $u->name }} ({{ $u->email }})
</option>
@endforeach
</select>
</div>
</div>

{{-- Fechas --}}
<div class="col-md-3">
<div class="form-group">
<label>Fecha compromiso</label>
<input type="date" class="form-control" name="commitment_date"
value="{{ old('commitment_date', optional($plan->commitment_date)->format('Y-m-d')) }}">
</div>
</div>

<div class="col-md-3">
<div class="form-group">
<label>Fecha cierre</label>
<input type="date" class="form-control" name="close_date"
value="{{ old('close_date', optional($plan->close_date)->format('Y-m-d')) }}">
</div>
</div>

{{-- Status --}}
<div class="col-md-3">
<div class="form-group">
<label>Status *</label>
<input class="form-control" name="status"
value="{{ old('status', $plan->status ?? 'ABIERTO') }}" required>
</div>
</div>

{{-- Progreso --}}
<div class="col-md-3">
<div class="form-group">
<label>Progreso</label>
<input class="form-control"
value="{{ $plan->progress ?? 0 }}%" disabled>
</div>
</div>

{{-- Observaciones --}}
<div class="col-12">
<div class="form-group">
<label>Observaciones</label>
<textarea class="form-control" name="notes" rows="2">{{ old('notes', $plan->notes) }}</textarea>
</div>
</div>

</div>

<button class="btn btn-primary">Guardar</button>
<a class="btn btn-outline-secondary" href="{{ route('quality.plans.index') }}">Cancelar</a>

</form>

</div>
</div>

@stop