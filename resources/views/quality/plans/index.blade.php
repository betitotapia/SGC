@extends('adminlte::page')

@section('title', 'Planes de Acción')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Planes de Acción (Calidad)</h1>
    @can('quality.plans.create')
      <a class="btn btn-primary" href="{{ route('quality.plans.create') }}">Nuevo plan</a>
    @endcan
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <form class="row mb-3" method="GET" action="{{ route('quality.plans.index') }}">
    <div class="col-md-8">
      <input class="form-control" name="q" value="{{ $q }}" placeholder="Buscar por folio, hallazgo, depto, responsable, status...">
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-outline-secondary w-100" type="submit">Buscar</button>
      <a class="btn btn-outline-dark w-100" href="{{ route('quality.plans.index') }}">Limpiar</a>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Status (manual)</th>
              <th style="width: 220px;">Progreso</th>
              <th>Depto.</th>
              <th>Responsable</th>
              <th class="text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plans as $plan)
              <tr>
                <td class="font-weight-bold">{{ $plan->folio }}</td>
                <td><span class="badge badge-secondary">{{ $plan->status }}</span></td>
                <td>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{ $plan->progress }}%;">{{ $plan->progress }}%</div>
                  </div>
                </td>
                <td>{{ optional($plan->department)->name }}</td>
                <td>{{ $plan->owner_name }}</td>
                <td class="text-right">
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('quality.plans.show', $plan) }}">Ver</a>
                  @can('quality.plans.update')
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('quality.plans.edit', $plan) }}">Editar</a>
                  @endcan
                  @can('quality.plans.delete')
                    <form class="d-inline" method="POST" action="{{ route('quality.plans.destroy', $plan) }}" onsubmit="return confirm('¿Eliminar plan?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                  @endcan
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4">Sin registros</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $plans->links() }}
  </div>
@stop
