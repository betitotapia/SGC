@extends('adminlte::page')

@section('title', 'Firma Digital — ' . $document->folio)

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h1 class="m-0">Firma digital</h1>
      <small class="text-muted">{{ $document->folio }} — {{ $document->title }}</small>
    </div>
    <a class="btn btn-outline-secondary btn-sm"
       href="{{ route('quality.documents.show', $document) }}">Volver al documento</a>
  </div>
@stop

@section('content')
@php
  $user = auth()->user();
  $roleColors = ['author' => 'secondary', 'reviewer' => 'warning', 'approver' => 'success'];
@endphp

<div class="row justify-content-center">
  <div class="col-lg-8">

    {{-- ── Info del documento ──────────────────────────────────────── --}}
    <div class="card mb-3">
      <div class="card-header font-weight-bold">
        <i class="fas fa-file-alt mr-1 text-muted"></i>Documento a firmar
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <dl class="mb-0">
              <dt class="text-muted small">Folio</dt>
              <dd class="font-weight-bold">{{ $document->folio }}</dd>
              <dt class="text-muted small">Título</dt>
              <dd>{{ $document->title }}</dd>
              <dt class="text-muted small">Tipo</dt>
              <dd>{{ $document->typeLabel() }}</dd>
            </dl>
          </div>
          <div class="col-md-6">
            <dl class="mb-0">
              <dt class="text-muted small">Departamento</dt>
              <dd>{{ optional($document->department)->name ?? '—' }}</dd>
              <dt class="text-muted small">Versión</dt>
              <dd>v{{ $version->version_number }}</dd>
              <dt class="text-muted small">Motivo del cambio</dt>
              <dd>{{ $version->change_reason ?? '—' }}</dd>
            </dl>
          </div>
        </div>

        @if($version->file_path)
          <hr>
          <a href="{{ route('quality.documents.versions.download', [$document, $version]) }}"
             target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-download mr-1"></i>
            Revisar archivo — {{ $version->original_name }}
          </a>
          <small class="text-muted ml-2">Debes revisar el documento antes de firmar.</small>
        @endif
      </div>
    </div>

    {{-- ── Estado de firmas ────────────────────────────────────────── --}}
    <div class="card mb-3">
      <div class="card-header font-weight-bold">
        <i class="fas fa-users mr-1 text-muted"></i>Cadena de firmas
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($version->approvals->sortBy('order') as $a)
            @php
              $isMe   = $a->id === $approval->id;
              $border = $isMe ? 'border-warning' : ($a->status === 'approved' ? 'border-success' : 'border-light');
            @endphp
            <div class="col-md-4 mb-2">
              <div class="card {{ $border }} {{ $isMe ? 'shadow-sm' : '' }} h-100">
                <div class="card-body py-2 px-3">
                  <div class="small font-weight-bold text-muted text-uppercase mb-1">
                    {{ \App\Models\DocumentApproval::ROLE_LABELS[$a->role_in_approval] ?? $a->role_in_approval }}
                  </div>
                  <div class="font-weight-bold">
                    {{ optional($a->user)->name ?? '—' }}
                    @if($isMe)
                      <span class="badge badge-warning ml-1">Tú</span>
                    @endif
                  </div>
                  @if($a->status === 'approved' && $a->signed_at)
                    <div class="small text-success mt-1">
                      <i class="fas fa-check-circle"></i> Firmado {{ $a->signed_at->format('d/m/Y H:i') }}
                    </div>
                  @elseif($isMe)
                    <div class="small text-warning mt-1">
                      <i class="fas fa-clock"></i> Pendiente de tu firma
                    </div>
                  @else
                    <div class="small text-muted mt-1">Pendiente</div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── Formulario de firma digital ─────────────────────────────── --}}
    <div class="card border-warning">
      <div class="card-header bg-warning font-weight-bold">
        <i class="fas fa-signature mr-1"></i>
        Tu firma digital como
        <strong>{{ \App\Models\DocumentApproval::ROLE_LABELS[$approval->role_in_approval] ?? $approval->role_in_approval }}</strong>
      </div>
      <div class="card-body">

        {{-- Opción de firma móvil --}}
        <div class="alert alert-light border d-flex align-items-center justify-content-between mb-3 py-2">
          <span class="text-muted small">
            <i class="fas fa-mobile-alt mr-1"></i>
            ¿Prefieres firmar con el dedo desde tu celular?
          </span>
          <button type="button" id="mobile-sign-btn" class="btn btn-outline-secondary btn-sm ml-3">
            <i class="fas fa-qrcode mr-1"></i>Generar código QR
          </button>
        </div>

        {{-- Datos del firmante (solo lectura) --}}
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="text-muted small d-block">Nombre</label>
            <strong>{{ $user->name }}</strong>
          </div>
          <div class="col-md-4">
            <label class="text-muted small d-block">Correo</label>
            <span>{{ $user->email }}</span>
          </div>
          <div class="col-md-4">
            <label class="text-muted small d-block">Fecha y hora</label>
            <span id="current-datetime">{{ now()->format('d/m/Y H:i') }}</span>
          </div>
        </div>

        <form method="POST"
              action="{{ route('quality.documents.approvals.sign', [$document, $approval]) }}"
              id="sign-form">
          @csrf

          {{-- ── Canvas de firma autógrafa ──────────────────────────── --}}
          <div class="form-group">
            <label class="font-weight-bold">
              <i class="fas fa-pen-nib mr-1 text-warning"></i>
              Firma autógrafa <span class="text-danger">*</span>
            </label>
            <div class="position-relative border rounded" style="background:#fff; cursor:crosshair;">
              <canvas id="signature-canvas"
                      style="width:100%; height:180px; display:block; touch-action:none;">
              </canvas>
              <button type="button" id="clear-sig"
                      class="btn btn-xs btn-outline-secondary"
                      style="position:absolute; top:8px; right:8px;">
                <i class="fas fa-eraser mr-1"></i>Limpiar
              </button>
            </div>
            <small class="text-muted">
              Dibuja tu firma con el mouse o el dedo (en dispositivos táctiles).
            </small>
            <div id="sig-error" class="text-danger small mt-1" style="display:none;">
              Debes dibujar tu firma antes de aprobar.
            </div>
            <input type="hidden" name="signature_data" id="signature_data">
          </div>

          {{-- Comentarios --}}
          <div class="form-group">
            <label>Comentarios <span class="text-muted">(opcionales para aprobar)</span></label>
            <textarea class="form-control @error('comments') is-invalid @enderror"
                      name="comments" rows="2" id="sign-comments"
                      placeholder="Observaciones sobre este documento..."></textarea>
            @error('comments')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Checkbox de confirmación --}}
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="confirmed"
                     name="confirmed" value="1">
              <label class="custom-control-label font-weight-bold" for="confirmed">
                He revisado el documento y confirmo mi firma digital en representación de mi cargo.
              </label>
            </div>
            @error('confirmed')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="action" value="approve"
                    class="btn btn-success btn-lg"
                    id="approve-btn" disabled>
              <i class="fas fa-check mr-1"></i>Aprobar y firmar
            </button>

            <button type="button" class="btn btn-outline-danger"
                    data-toggle="modal" data-target="#reject-modal">
              <i class="fas fa-times mr-1"></i>Rechazar documento
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

{{-- Modal de firma móvil (QR) --}}
<div class="modal fade" id="mobile-modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title mb-0">
          <i class="fas fa-mobile-alt mr-1 text-secondary"></i>Firmar desde celular
        </h6>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p class="text-muted small mb-3">
          Escanea el código QR con la cámara de tu celular o copia el enlace y ábrelo en el navegador.
        </p>
        <div id="qr-div" class="d-flex justify-content-center mb-3"
             style="min-height:224px; align-items:center;"></div>
        <div class="input-group mb-2">
          <input type="text" id="mobile-url" class="form-control form-control-sm"
                 readonly style="font-size:0.75rem; color:#555;">
          <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="copy-url-btn">
              <i class="fas fa-copy"></i>
            </button>
          </div>
        </div>
        <small class="text-muted">
          <i class="fas fa-clock mr-1"></i>Expira: <strong id="mobile-expires"></strong>
        </small>
        <div class="alert alert-warning small mt-3 mb-0 text-left py-2">
          <i class="fas fa-info-circle mr-1"></i>
          Una vez que firmes desde el celular, esta página se actualizará automáticamente.
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal de rechazo --}}
<div class="modal fade" id="reject-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST"
            action="{{ route('quality.documents.approvals.sign', [$document, $approval]) }}">
        @csrf
        <input type="hidden" name="action" value="reject">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="fas fa-times-circle mr-1"></i>Rechazar documento
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p class="text-muted">
            El documento regresará a <strong>Borrador</strong> y se notificará al creador con tu motivo.
          </p>
          <div class="form-group mb-0">
            <label>Motivo del rechazo *</label>
            <textarea class="form-control" name="comments" rows="4" required minlength="10"
                      placeholder="Describe claramente qué debe corregirse antes de aprobarlo..."></textarea>
            <small class="text-muted">Mínimo 10 caracteres. Este mensaje llegará al autor.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"
                  onclick="return confirm('¿Confirmas el rechazo? El autor será notificado.');">
            Rechazar y notificar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('css')
<style>
  #signature-canvas { cursor: crosshair; }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
  // ── Inicializar SignaturePad ──────────────────────────────────────────────
  const canvas = document.getElementById('signature-canvas');

  // Ajustar resolución para pantallas HiDPI
  function resizeCanvas() {
    const ratio  = Math.max(window.devicePixelRatio || 1, 1);
    const rect   = canvas.getBoundingClientRect();
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    signaturePad.clear();
  }

  const signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(255,255,255)',
    penColor: '#1a1a2e',
    minWidth: 1,
    maxWidth: 2.5,
  });

  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);

  // ── Limpiar canvas ───────────────────────────────────────────────────────
  document.getElementById('clear-sig').addEventListener('click', function () {
    signaturePad.clear();
    updateApproveBtn();
  });

  // ── Habilitar botón: requiere firma dibujada + checkbox marcado ──────────
  function updateApproveBtn() {
    const checked = document.getElementById('confirmed').checked;
    const hasSig  = !signaturePad.isEmpty();
    document.getElementById('approve-btn').disabled = !(checked && hasSig);
  }

  document.getElementById('confirmed').addEventListener('change', updateApproveBtn);
  signaturePad.addEventListener('endStroke', updateApproveBtn);

  // ── Al enviar: inyectar datos de firma en el campo oculto ────────────────
  document.getElementById('sign-form').addEventListener('submit', function (e) {
    if (signaturePad.isEmpty()) {
      e.preventDefault();
      document.getElementById('sig-error').style.display = 'block';
      canvas.parentElement.style.borderColor = '#dc3545';
      return;
    }
    document.getElementById('sig-error').style.display = 'none';
    canvas.parentElement.style.borderColor = '';
    document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
  });

  // ── Polling: detectar si la firma fue procesada (desde celular) ──────────
  var checkUrl   = '{{ route("quality.documents.approvals.check-pending", [$document, $approval]) }}';
  var redirectTo = '{{ route("quality.documents.show", $document) }}';
  var pollTimer  = null;

  function checkIfSigned() {
    fetch(checkUrl, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.pending) {
        clearInterval(pollTimer);
        // Mostrar aviso y redirigir
        document.body.insertAdjacentHTML('beforeend',
          '<div style="position:fixed;top:0;left:0;width:100%;background:#28a745;color:#fff;' +
          'text-align:center;padding:14px;font-size:1rem;z-index:9999;">' +
          '<i class="fas fa-check-circle mr-2"></i>Firma registrada desde el celular. Redirigiendo...</div>'
        );
        setTimeout(function () { window.location.href = redirectTo; }, 1800);
      }
    })
    .catch(function () {}); // ignorar errores de red
  }

  // Arrancar polling cada 10 segundos
  pollTimer = setInterval(checkIfSigned, 10000);

  // ── Botón "Firmar desde celular" ────────────────────────────────────────
  document.getElementById('mobile-sign-btn').addEventListener('click', async function () {
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Generando...';

    var data;
    try {
      var resp = await fetch('{{ route("quality.documents.approvals.mobile-token", [$document, $approval]) }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
      });
      data = await resp.json();
    } catch (e) {
      alert('Error al conectar con el servidor. Intenta de nuevo.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-qrcode mr-1"></i>Generar código QR';
      return;
    }

    if (!data.url) {
      alert(data.error || 'No se pudo generar el enlace.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-qrcode mr-1"></i>Generar código QR';
      return;
    }

    document.getElementById('mobile-url').value = data.url;
    document.getElementById('mobile-expires').textContent = data.expires_at;

    var qrDiv = document.getElementById('qr-div');
    qrDiv.innerHTML = '';
    try {
      new QRCode(qrDiv, {
        text: data.url,
        width: 220,
        height: 220,
        colorDark: '#1a3a5c',
        colorLight: '#ffffff',
      });
    } catch (qrErr) {
      qrDiv.innerHTML = '<small class="text-muted">QR no disponible — usa el enlace de abajo.</small>';
    }

    $('#mobile-modal').modal('show');

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-qrcode mr-1"></i>Generar código QR';
  });

  // Al cerrar el modal del QR: verificar inmediatamente si ya firmaron
  document.getElementById('mobile-modal').addEventListener('hidden.bs.modal', function () {
    checkIfSigned();
  });

  // Copiar URL al portapapeles
  document.getElementById('copy-url-btn').addEventListener('click', function () {
    var input = document.getElementById('mobile-url');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    this.innerHTML = '<i class="fas fa-check text-success"></i>';
    var self = this;
    setTimeout(function () { self.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);
  });

  // ── Reloj en tiempo real ─────────────────────────────────────────────────
  function updateTime() {
    var now = new Date();
    var pad = function(n) { return String(n).padStart(2, '0'); };
    document.getElementById('current-datetime').textContent =
      pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear() +
      ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes());
  }
  setInterval(updateTime, 60000);
})();
</script>
@endsection
