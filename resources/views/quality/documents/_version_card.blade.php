{{--
  Partial: _version_card
  Variables esperadas: $document, $draftVersion (nullable)
--}}

@php
  $canUpload = auth()->user()->can('documents.update');
  $canSubmit = $canUpload && $draftVersion;
@endphp

<div class="card mb-3">
  <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
    <span>
      {{ $draftVersion ? 'Versión en preparación — v' . $draftVersion->version_number : 'Nueva versión' }}
    </span>
    @if($draftVersion && $canUpload)
      <form method="POST"
            action="{{ route('quality.documents.versions.destroy', [$document, $draftVersion]) }}"
            onsubmit="return confirm('¿Eliminar esta versión borrador?');"
            class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">Eliminar borrador</button>
      </form>
    @endif
  </div>
  <div class="card-body">

    {{-- Si NO hay versión draft, mostrar formulario de upload --}}
    @if(! $draftVersion && $canUpload)
      <form method="POST"
            action="{{ route('quality.documents.versions.store', $document) }}"
            enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Archivo del documento *</label>
              <input type="file"
                     class="form-control-file @error('file') is-invalid @enderror"
                     name="file"
                     accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                     required>
              <small class="text-muted">PDF, Word, Excel o PowerPoint. Máximo 20 MB.</small>
              @error('file')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Fecha de vigencia</label>
              <input type="date" class="form-control" name="effective_date">
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-group w-100">
              <button class="btn btn-primary w-100">
                <i class="fas fa-upload mr-1"></i>
                {{ $document->currentVersion ? 'Subir nueva versión' : 'Subir archivo' }}
              </button>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label>Motivo del cambio</label>
              <textarea class="form-control" name="change_reason" rows="2"
                        placeholder="Describe brevemente por qué se actualiza este documento..."></textarea>
            </div>
          </div>
        </div>
      </form>
    @endif

    {{-- Si YA hay versión draft, mostrar su info + botón de enviar a revisión --}}
    @if($draftVersion)
      <div class="row">
        <div class="col-md-6">
          <dl class="mb-0">
            <dt class="text-muted small">Archivo</dt>
            <dd>
              <i class="fas fa-file mr-1 text-secondary"></i>
              {{ $draftVersion->original_name ?? '—' }}
              @if($draftVersion->size_bytes)
                <small class="text-muted">({{ number_format($draftVersion->size_bytes / 1024, 0) }} KB)</small>
              @endif
            </dd>

            @if($draftVersion->change_reason)
              <dt class="text-muted small">Motivo del cambio</dt>
              <dd>{{ $draftVersion->change_reason }}</dd>
            @endif

            @if($draftVersion->effective_date)
              <dt class="text-muted small">Fecha de vigencia propuesta</dt>
              <dd>{{ $draftVersion->effective_date->format('d/m/Y') }}</dd>
            @endif
          </dl>
        </div>
        <div class="col-md-6 d-flex align-items-start flex-column justify-content-center">
          @if($canSubmit)
            @if($document->hasSigners())
              <form method="POST"
                    action="{{ route('quality.documents.submit', $document) }}"
                    onsubmit="return confirm('¿Enviar este documento a revisión y firma? Se notificará al primer firmante.');">
                @csrf
                <button class="btn btn-warning">
                  <i class="fas fa-paper-plane mr-1"></i>
                  Enviar a revisión
                </button>
              </form>
              <small class="text-muted mt-1">
                El flujo de firmas se iniciará al confirmar.
              </small>
            @else
              <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                El documento no tiene firmantes asignados.
                <a href="{{ route('quality.documents.edit', $document) }}" class="alert-link">
                  Edita el documento
                </a>
                y asigna al menos un firmante antes de enviar a revisión.
              </div>
            @endif
          @endif
        </div>
      </div>
    @endif

  </div>
</div>
