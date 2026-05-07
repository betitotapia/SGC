<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Firma Digital — {{ $document->folio }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    * { box-sizing: border-box; }
    body { background: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; padding-bottom: 40px; }

    .top-bar {
      background: #1a3a5c; color: #fff; padding: 14px 18px;
      position: sticky; top: 0; z-index: 10;
    }
    .top-bar .role-pill {
      display: inline-block; background: rgba(255,255,255,0.2);
      border-radius: 20px; padding: 1px 12px; font-size: 0.8rem;
      margin-bottom: 4px;
    }
    .top-bar h5 { margin: 0; font-size: 1.1rem; }
    .top-bar small { opacity: 0.75; font-size: 0.82rem; }

    .canvas-wrapper {
      background: #fff; border: 2px dashed #ced4da; border-radius: 10px;
      position: relative; cursor: crosshair; touch-action: none;
      min-height: 200px;
    }
    .canvas-wrapper canvas { display: block; width: 100%; height: 200px; }

    .btn-sign {
      font-size: 1.05rem; padding: 14px; border-radius: 8px;
      letter-spacing: 0.3px;
    }
    .custom-control-label { cursor: pointer; }

    .reject-toggle { font-size: 0.88rem; color: #dc3545; background: none; border: none; padding: 0; }
  </style>
</head>
<body>

  {{-- Barra superior con info del documento --}}
  <div class="top-bar">
    <span class="role-pill">
      <i class="fas fa-signature mr-1"></i>
      {{ \App\Models\DocumentApproval::ROLE_LABELS[$approval->role_in_approval] ?? $approval->role_in_approval }}
    </span>
    <h5>{{ $document->folio }}</h5>
    <small>{{ $document->title }}</small>
  </div>

  <div class="container-fluid px-3 pt-3">

    {{-- Errores de validación --}}
    @if($errors->any())
      <div class="alert alert-danger py-2">
        @foreach($errors->all() as $error)
          <div><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    {{-- Info del firmante --}}
    <div class="card mb-3 shadow-sm">
      <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted" style="font-size:0.75rem;">Firmante</div>
            <strong>{{ optional($approval->user)->name }}</strong>
          </div>
          <div class="text-right">
            <div class="text-muted" style="font-size:0.75rem;">Fecha y hora</div>
            <span id="current-dt" style="font-size:0.9rem;">{{ now()->format('d/m/Y H:i') }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Formulario de aprobación --}}
    <form method="POST" action="{{ route('quality.mobile-sign.store', $token) }}" id="sign-form">
      @csrf
      <input type="hidden" name="action" value="approve">
      <input type="hidden" name="signature_data" id="signature_data">

      {{-- Canvas de firma --}}
      <div class="card mb-3 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
          <span class="font-weight-bold" style="font-size:0.95rem;">
            <i class="fas fa-pen-nib mr-1 text-warning"></i>Tu firma autógrafa
          </span>
          <button type="button" id="clear-sig" class="btn btn-outline-secondary btn-sm py-1">
            <i class="fas fa-eraser mr-1"></i>Limpiar
          </button>
        </div>
        <div class="card-body p-2">
          <div class="canvas-wrapper" id="canvas-wrapper">
            <canvas id="sig-canvas"></canvas>
          </div>
          <small class="text-muted d-block mt-1">
            <i class="fas fa-hand-pointer mr-1"></i>Dibuja tu firma con el dedo.
          </small>
        </div>
      </div>

      {{-- Comentarios opcionales --}}
      <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
          <label class="mb-1" style="font-size:0.85rem; color:#6c757d;">Comentarios <span class="text-muted">(opcionales)</span></label>
          <textarea class="form-control" name="comments" rows="2"
                    placeholder="Observaciones sobre el documento...">{{ old('comments') }}</textarea>
        </div>
      </div>

      {{-- Checkbox de confirmación --}}
      <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="confirmed"
                   name="confirmed" value="1" {{ old('confirmed') ? 'checked' : '' }}>
            <label class="custom-control-label font-weight-bold" for="confirmed"
                   style="font-size:0.92rem;">
              He revisado el documento y confirmo mi firma digital en representación de mi cargo.
            </label>
          </div>
        </div>
      </div>

      <button type="submit" id="approve-btn"
              class="btn btn-success btn-block btn-sign font-weight-bold mb-2" disabled>
        <i class="fas fa-check-circle mr-1"></i>Aprobar y firmar
      </button>
    </form>

    {{-- Opción de rechazo --}}
    <div class="text-center mb-2">
      <button class="reject-toggle" type="button" id="toggle-reject">
        <i class="fas fa-times-circle mr-1"></i>Rechazar documento
      </button>
    </div>

    <div id="reject-section" style="display:none;">
      <div class="card border-danger shadow-sm mb-3">
        <div class="card-header bg-danger text-white py-2 font-weight-bold">
          <i class="fas fa-times-circle mr-1"></i>Rechazar documento
        </div>
        <div class="card-body">
          <p class="text-muted small mb-2">
            El documento regresará a <strong>Borrador</strong> y el autor será notificado.
          </p>
          <form method="POST" action="{{ route('quality.mobile-sign.store', $token) }}">
            @csrf
            <input type="hidden" name="action" value="reject">
            <div class="form-group mb-2">
              <label class="small font-weight-bold">Motivo del rechazo <span class="text-danger">*</span></label>
              <textarea class="form-control" name="comments" rows="4" required minlength="10"
                        placeholder="Describe claramente qué debe corregirse..."></textarea>
              <small class="text-muted">Mínimo 10 caracteres.</small>
            </div>
            <button type="submit" class="btn btn-danger btn-block"
                    onclick="return confirm('¿Confirmas el rechazo? El autor será notificado.')">
              <i class="fas fa-times mr-1"></i>Confirmar rechazo
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
  <script>
  (function () {
    const canvas  = document.getElementById('sig-canvas');
    const wrapper = document.getElementById('canvas-wrapper');

    const pad = new SignaturePad(canvas, {
      backgroundColor: 'rgb(255,255,255)',
      penColor: '#1a1a2e',
      minWidth: 1.5,
      maxWidth: 3.5,
    });

    function resizeCanvas() {
      const strokes = pad.toData(); // guardar trazos antes de limpiar
      const ratio   = Math.max(window.devicePixelRatio || 1, 1);
      const rect    = canvas.getBoundingClientRect();
      canvas.width  = rect.width  * ratio;
      canvas.height = rect.height * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
      pad.clear();
      if (strokes && strokes.length) {
        pad.fromData(strokes); // restaurar trazos
      }
    }

    // Inicializar
    resizeCanvas();

    // Solo redimensionar cuando cambia el ANCHO (el teclado móvil solo cambia el alto)
    var lastWidth = Math.round(wrapper.getBoundingClientRect().width);
    var ro = new ResizeObserver(function (entries) {
      var newWidth = Math.round(entries[0].contentRect.width);
      if (Math.abs(newWidth - lastWidth) > 2) {
        lastWidth = newWidth;
        resizeCanvas();
      }
    });
    ro.observe(wrapper);

    document.getElementById('clear-sig').addEventListener('click', function () {
      pad.clear();
      wrapper.style.borderColor = '#ced4da';
      updateBtn();
    });

    function updateBtn() {
      const ok = !pad.isEmpty() && document.getElementById('confirmed').checked;
      document.getElementById('approve-btn').disabled = !ok;
    }

    document.getElementById('confirmed').addEventListener('change', updateBtn);
    pad.addEventListener('endStroke', updateBtn);

    document.getElementById('sign-form').addEventListener('submit', function (e) {
      if (pad.isEmpty()) {
        e.preventDefault();
        wrapper.style.borderColor = '#dc3545';
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
      wrapper.style.borderColor = '#ced4da';
      document.getElementById('signature_data').value = pad.toDataURL('image/png');
    });

    // Mostrar/ocultar sección de rechazo
    document.getElementById('toggle-reject').addEventListener('click', function () {
      var s = document.getElementById('reject-section');
      s.style.display = s.style.display === 'none' ? 'block' : 'none';
    });

    // Reloj en tiempo real
    function tick() {
      var now = new Date();
      var p = function(n) { return String(n).padStart(2, '0'); };
      document.getElementById('current-dt').textContent =
        p(now.getDate()) + '/' + p(now.getMonth()+1) + '/' + now.getFullYear() +
        ' ' + p(now.getHours()) + ':' + p(now.getMinutes());
    }
    setInterval(tick, 60000);
  })();
  </script>
</body>
</html>
