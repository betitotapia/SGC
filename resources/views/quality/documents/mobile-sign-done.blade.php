<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $success ? 'Firma registrada' : 'Documento rechazado' }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; }
    .done-icon { font-size: 5rem; }
  </style>
</head>
<body>
  <div class="container py-5 text-center px-4">
    @if($success)
      <div class="text-success done-icon mb-3">
        <i class="fas fa-check-circle"></i>
      </div>
      <h4 class="font-weight-bold text-success mb-2">{{ $message }}</h4>
      @if(!empty($published))
        <p class="text-muted">
          El documento ha sido <strong>publicado</strong>. Todas las firmas fueron completadas
          y la constancia de firmas fue generada.
        </p>
      @else
        <p class="text-muted">
          Tu firma fue registrada correctamente. El siguiente firmante será notificado.
        </p>
      @endif
    @else
      <div class="text-danger done-icon mb-3">
        <i class="fas fa-times-circle"></i>
      </div>
      <h4 class="font-weight-bold text-danger mb-2">{{ $message }}</h4>
      <p class="text-muted">El documento regresará a borrador y el autor recibirá una notificación.</p>
    @endif

    <hr class="my-4">
    <p class="text-muted small mb-0">
      <i class="fas fa-lock mr-1"></i>
      Puedes cerrar esta ventana con seguridad.
    </p>
  </div>
</body>
</html>
