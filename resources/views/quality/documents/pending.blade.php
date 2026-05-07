@extends('adminlte::page')

@section('title', 'Mis firmas pendientes')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">
      <i class="fas fa-signature mr-2 text-warning"></i>Mis firmas pendientes
      @if($pendingApprovals->isNotEmpty())
        <span class="badge badge-warning ml-1">{{ $pendingApprovals->count() }}</span>
      @endif
    </h1>
  </div>
@stop

@section('content')

  @if($pendingApprovals->isEmpty())
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
        <h4 class="font-weight-bold">¡Sin pendientes!</h4>
        <p class="text-muted">No tienes documentos pendientes de firma en este momento.</p>
        <a class="btn btn-outline-primary mt-2" href="{{ route('quality.documents.index') }}">
          Ver documentos
        </a>
      </div>
    </div>
  @else
    <div class="row">
      @foreach($pendingApprovals as $approval)
        @php
          $doc     = $approval->version->document;
          $version = $approval->version;
          $typeColors = [
            'procedure'        => 'primary',
            'process'          => 'info',
            'format'           => 'secondary',
            'work_instruction' => 'warning',
          ];
          $roleColors = ['author' => 'secondary', 'reviewer' => 'warning', 'approver' => 'success'];
          $alreadySigned = $version->approvals->where('status', 'approved')->count();
          $total         = $version->approvals->count();
        @endphp
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card border-warning h-100">
            <div class="card-header bg-warning d-flex justify-content-between align-items-center py-2">
              <span class="font-weight-bold">{{ $doc->folio }}</span>
              <span class="badge badge-light border">
                v{{ $version->version_number }}
              </span>
            </div>
            <div class="card-body">
              <p class="font-weight-bold mb-1">{{ $doc->title }}</p>
              <p class="text-muted small mb-2">
                <i class="fas fa-building mr-1"></i>
                {{ optional($doc->department)->name ?? '—' }}
              </p>

              <div class="mb-2">
                <span class="badge badge-{{ $typeColors[$doc->type] ?? 'secondary' }}">
                  {{ $doc->typeLabel() }}
                </span>
                <span class="badge badge-{{ $roleColors[$approval->role_in_approval] ?? 'secondary' }} ml-1">
                  Tu rol: {{ \App\Models\DocumentApproval::ROLE_LABELS[$approval->role_in_approval] ?? '—' }}
                </span>
              </div>

              {{-- Progreso de firmas --}}
              <div class="mb-2">
                <small class="text-muted">
                  {{ $alreadySigned }} de {{ $total }} firmas completadas
                </small>
                <div class="progress mt-1" style="height: 6px;">
                  <div class="progress-bar bg-warning"
                       style="width: {{ $total > 0 ? ($alreadySigned/$total)*100 : 0 }}%"></div>
                </div>
              </div>

              @if($version->submitted_at)
                <small class="text-muted">
                  <i class="fas fa-clock mr-1"></i>
                  Enviado: {{ $version->submitted_at->format('d/m/Y H:i') }}
                </small>
              @endif
            </div>
            <div class="card-footer p-2 d-flex gap-2">
              <a href="{{ route('quality.documents.approvals.show-sign', [$doc, $approval]) }}"
                 class="btn btn-warning btn-sm flex-grow-1">
                <i class="fas fa-pen-nib mr-1"></i>Revisar y firmar
              </a>
              <a href="{{ route('quality.documents.show', $doc) }}"
                 class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

@stop
