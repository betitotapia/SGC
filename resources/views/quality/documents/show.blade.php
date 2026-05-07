@extends('adminlte::page')

@section('title', $document->folio)

@section('content_header')
  @php
    $statusColors = [
      'draft'       => 'secondary',
      'in_review'   => 'warning',
      'in_approval' => 'info',
      'published'   => 'success',
      'obsolete'    => 'dark',
    ];
    $typeColors = [
      'procedure'        => 'primary',
      'process'          => 'info',
      'format'           => 'secondary',
      'work_instruction' => 'warning',
    ];

    $draftVersion  = $document->versions->firstWhere('status', 'draft');
    $activeVersion = $document->versions->whereIn('status', ['in_review', 'in_approval'])->first();
  @endphp

  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h1 class="m-0">
        {{ $document->folio }}
        <span class="badge badge-{{ $statusColors[$document->status] ?? 'secondary' }} ml-2" style="font-size:0.7rem;">
          {{ $document->statusLabel() }}
        </span>
        <span class="badge badge-{{ $typeColors[$document->type] ?? 'secondary' }} ml-1" style="font-size:0.7rem;">
          {{ $document->typeLabel() }}
        </span>
      </h1>
      <small class="text-muted">{{ $document->title }}</small>
    </div>
    <div class="d-flex gap-2">
      @can('documents.update')
        @if($document->isEditable())
          <a class="btn btn-outline-secondary btn-sm"
             href="{{ route('quality.documents.edit', $document) }}">
            <i class="fas fa-edit mr-1"></i>Editar
          </a>
        @endif
      @endcan
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('quality.documents.index') }}">Volver</a>
    </div>
  </div>
@stop

@section('content')
  @include('quality._partials.flash')

  <div class="row">

    {{-- ── Columna principal ────────────────────────────────────────── --}}
    <div class="col-md-8">

      {{-- Datos generales --}}
      <div class="card mb-3">
        <div class="card-header font-weight-bold">Datos del documento</div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <dl class="mb-0">
                <dt class="text-muted small">Folio</dt>
                <dd class="font-weight-bold">{{ $document->folio }}</dd>

                <dt class="text-muted small">Título</dt>
                <dd>{{ $document->title }}</dd>

                <dt class="text-muted small">Tipo</dt>
                <dd>
                  <span class="badge badge-{{ $typeColors[$document->type] ?? 'secondary' }}">
                    {{ $document->typeLabel() }}
                  </span>
                </dd>
              </dl>
            </div>
            <div class="col-md-6">
              <dl class="mb-0">
                <dt class="text-muted small">Departamento</dt>
                <dd>{{ optional($document->department)->name ?? '—' }}</dd>

                <dt class="text-muted small">Creado por</dt>
                <dd>{{ optional($document->creator)->name ?? '—' }}</dd>

                <dt class="text-muted small">Fecha de creación</dt>
                <dd>{{ $document->created_at->format('d/m/Y') }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Versión vigente (solo si está publicado) ──────────────── --}}
      @if($document->currentVersion && $document->status === \App\Models\Document::STATUS_PUBLISHED)
        <div class="card mb-3 border-success">
          <div class="card-header bg-success text-white font-weight-bold d-flex justify-content-between align-items-center">
            <span>Versión vigente — v{{ $document->currentVersion->version_number }}</span>
            @can('documents.update')
              <form method="POST"
                    action="{{ route('quality.documents.versions.store', $document) }}"
                    enctype="multipart/form-data"
                    id="new-version-form">
                @csrf
                <input type="file" name="file" id="new-version-file" class="d-none"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                       onchange="document.getElementById('new-version-form').submit();">
                <button type="button" class="btn btn-sm btn-light"
                        onclick="document.getElementById('new-version-file').click();">
                  <i class="fas fa-plus mr-1"></i>Iniciar nueva versión
                </button>
              </form>
            @endcan
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <dt class="text-muted small">Fecha de vigencia</dt>
                <dd>{{ optional($document->currentVersion->effective_date)->format('d/m/Y') ?? '—' }}</dd>

                <dt class="text-muted small">Publicado</dt>
                <dd>{{ optional($document->currentVersion->approved_at)->format('d/m/Y H:i') ?? '—' }}</dd>
              </div>
              <div class="col-md-6">
                @if($document->currentVersion->file_path)
                  <a href="{{ route('quality.documents.versions.download', [$document, $document->currentVersion]) }}"
                     target="_blank" class="btn btn-success mb-2">
                    <i class="fas fa-download mr-1"></i>
                    Descargar — {{ $document->currentVersion->original_name }}
                  </a>
                @endif
                @if($document->currentVersion->certificate_path)
                  <br>
                  <a href="{{ route('quality.documents.versions.certificate', [$document, $document->currentVersion]) }}"
                     target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-file-signature mr-1"></i>
                    Constancia de firmas (PDF)
                  </a>
                @endif
              </div>
            </div>

            {{-- Tabla de firmas de la versión vigente --}}
            @if($document->currentVersion->approvals->isNotEmpty())
              <hr>
              <div class="row">
                @foreach($document->currentVersion->approvals->sortBy('order') as $approval)
                  <div class="col-md-4 text-center border-right">
                    <div class="small text-muted text-uppercase font-weight-bold mb-1">{{ $approval->roleLabel() }}</div>
                    <div>{{ optional($approval->user)->name ?? '—' }}</div>
                    @if($approval->signed_at)
                      <div class="small text-success">
                        <i class="fas fa-check-circle"></i>
                        {{ $approval->signed_at->format('d/m/Y') }}
                      </div>
                    @endif
                                <div class="mt-2 border rounded bg-white d-flex align-items-center justify-content-center"
                         style="min-height:90px; width:100%; max-width:220px; margin:0 auto;">
                      @if($approval->signature_image && Storage::disk('local')->exists($approval->signature_image))
                        <img src="{{ route('quality.documents.approvals.signature', $approval) }}"
                             alt="Firma"
                             class="sig-thumb"
                             data-src="{{ route('quality.documents.approvals.signature', $approval) }}"
                             data-signer="{{ optional($approval->user)->name }}"
                             data-role="{{ $approval->roleLabel() }}"
                             style="max-height:85px; max-width:210px; display:block; margin:auto; padding:4px; cursor:zoom-in;">
                      @else
                        <span class="text-muted small" style="font-style:italic;">Sin firma</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- ── Flujo de firmas activo ─────────────────────────────────── --}}
      @if($activeVersion)
        @include('quality.documents._approval_chain', ['activeVersion' => $activeVersion->load('approvals.user')])
      @endif

      {{-- ── Tarjeta de versión en borrador ────────────────────────── --}}
      @if(in_array($document->status, [\App\Models\Document::STATUS_DRAFT]))
        @include('quality.documents._version_card', ['draftVersion' => $draftVersion])
      @endif

    </div>

    {{-- ── Historial de versiones ───────────────────────────────────── --}}
    <div class="col-md-4">
      <div class="card">
        <div class="card-header font-weight-bold">Historial de versiones</div>
        <div class="card-body p-0">
          @forelse($document->versions as $version)
            @php
              $vColors = [
                'draft'       => 'secondary',
                'in_review'   => 'warning',
                'in_approval' => 'info',
                'approved'    => 'success',
                'superseded'  => 'dark',
              ];
              $vLabels = [
                'draft'       => 'Borrador',
                'in_review'   => 'En revisión',
                'in_approval' => 'En aprobación',
                'approved'    => 'Aprobada',
                'superseded'  => 'Sustituida',
              ];
            @endphp
            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="font-weight-bold">v{{ $version->version_number }}</span>
                  <span class="badge badge-{{ $vColors[$version->status] ?? 'secondary' }} ml-1">
                    {{ $vLabels[$version->status] ?? $version->status }}
                  </span>
                </div>
                <small class="text-muted">{{ $version->created_at->format('d/m/Y') }}</small>
              </div>

              @if($version->change_reason)
                <small class="text-muted d-block mt-1">{{ $version->change_reason }}</small>
              @endif

              @if($version->submitter)
                <small class="text-muted">Por: {{ $version->submitter->name }}</small>
              @endif

              {{-- Mini resumen de firmas --}}
              @if($version->approvals->isNotEmpty())
                <div class="mt-1 d-flex gap-1 flex-wrap">
                  @foreach($version->approvals->sortBy('order') as $approval)
                    @php
                      $aColor = match($approval->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'secondary',
                      };
                    @endphp
                    <span class="badge badge-{{ $aColor }}"
                          title="{{ $approval->roleLabel() }}: {{ optional($approval->user)->name }}">
                      {{ $approval->roleLabel() }}
                    </span>
                  @endforeach
                </div>
              @endif

              {{-- Botón eliminar versión draft --}}
              @if($version->status === 'draft')
                @can('documents.update')
                  <form method="POST"
                        action="{{ route('quality.documents.versions.destroy', [$document, $version]) }}"
                        onsubmit="return confirm('¿Eliminar esta versión borrador?');"
                        class="mt-1">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger">
                      <i class="fas fa-trash-alt"></i> Eliminar borrador
                    </button>
                  </form>
                @endcan
              @endif
            </div>
          @empty
            <div class="p-3 text-muted text-center">Sin versiones aún</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>
@stop

{{-- Modal para ampliar firma --}}
<div class="modal fade" id="sig-modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title mb-0">
          <i class="fas fa-signature mr-1 text-secondary"></i>
          <span id="sig-modal-role"></span> — <span id="sig-modal-signer"></span>
        </h6>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center bg-light" style="min-height:200px; display:flex; align-items:center; justify-content:center;">
        <img id="sig-modal-img" src="" alt="Firma"
             style="max-width:100%; max-height:380px; border:1px solid #dee2e6; border-radius:4px; background:#fff; padding:8px;">
      </div>
    </div>
  </div>
</div>

@push('js')
<script>
document.querySelectorAll('.sig-thumb').forEach(function (img) {
  img.addEventListener('click', function () {
    document.getElementById('sig-modal-img').src    = this.dataset.src;
    document.getElementById('sig-modal-role').textContent   = this.dataset.role;
    document.getElementById('sig-modal-signer').textContent = this.dataset.signer;
    $('#sig-modal').modal('show');
  });
});

@if(in_array($document->status, ['in_review', 'in_approval']))
// Polling automático mientras el documento está en proceso de firma
(function () {
  var current  = '{{ $document->status }}';
  var pollUrl  = '{{ route("quality.documents.poll-status", $document) }}';
  var interval = setInterval(function () {
    fetch(pollUrl, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.status !== current) {
        clearInterval(interval);
        location.reload();
      }
    })
    .catch(function () {}); // ignorar errores de red silenciosamente
  }, 15000); // cada 15 segundos
})();
@endif
</script>
@endpush

@stack('js')
