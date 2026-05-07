{{--
  Partial: _approval_chain
  Variables esperadas: $document, $activeVersion (DocumentVersion con approvals cargadas)
--}}

@php
  $currentUser     = auth()->user();
  $nextApproval    = $activeVersion->nextPendingApproval();
  $isMyTurn        = $nextApproval && $nextApproval->user_id === $currentUser->id;
@endphp

<div class="card mb-3">
  <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
    <span>
      Flujo de firmas — v{{ $activeVersion->version_number }}
    </span>
    <span class="badge badge-{{ $document->status === 'in_approval' ? 'info' : 'warning' }}">
      {{ $document->statusLabel() }}
    </span>
  </div>
  <div class="card-body">

    {{-- Cadena de firmantes --}}
    <div class="row mb-3">
      @foreach($activeVersion->approvals->sortBy('order') as $approval)
        @php
          $cardBorder = match($approval->status) {
            'approved' => 'border-success',
            'rejected' => 'border-danger',
            default    => ($nextApproval && $nextApproval->id === $approval->id)
                          ? 'border-warning'
                          : 'border-secondary',
          };
          $icon = match($approval->status) {
            'approved' => '<i class="fas fa-check-circle text-success"></i>',
            'rejected' => '<i class="fas fa-times-circle text-danger"></i>',
            default    => ($nextApproval && $nextApproval->id === $approval->id)
                          ? '<i class="fas fa-clock text-warning"></i>'
                          : '<i class="far fa-circle text-muted"></i>',
          };
        @endphp
        <div class="col-md-4 mb-2">
          <div class="card {{ $cardBorder }} h-100">
            <div class="card-body py-2 px-3">
              <div class="d-flex justify-content-between align-items-start">
                <small class="font-weight-bold text-muted text-uppercase">
                  {{ $approval->roleLabel() }}
                </small>
                {!! $icon !!}
              </div>
              <div class="font-weight-bold">{{ optional($approval->user)->name ?? '—' }}</div>
              @if($approval->signed_at)
                <div class="small text-muted">{{ $approval->signed_at->format('d/m/Y H:i') }}</div>
              @else
                <div class="small text-muted">Pendiente</div>
              @endif
              @if($approval->comments)
                <div class="small mt-1 p-1 bg-light rounded">
                  <i class="fas fa-comment-alt fa-xs mr-1"></i>{{ $approval->comments }}
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Acción de firma: redirige a la página dedicada con canvas autógrafo --}}
    @if($isMyTurn)
      <hr>
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="font-weight-bold mb-0">
          <i class="fas fa-signature mr-1 text-warning"></i>
          Es tu turno de firmar como <strong>{{ $nextApproval->roleLabel() }}</strong>
        </h6>
        <a href="{{ route('quality.documents.approvals.show-sign', [$document, $nextApproval]) }}"
           class="btn btn-warning">
          <i class="fas fa-pen-nib mr-1"></i>Ir a firmar el documento
        </a>
      </div>
    @elseif($nextApproval)
      <div class="alert alert-info mb-0">
        <i class="fas fa-hourglass-half mr-1"></i>
        Esperando la firma de <strong>{{ optional($nextApproval->user)->name }}</strong>
        como {{ $nextApproval->roleLabel() }}.
      </div>
    @endif

  </div>
</div>

