<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enlace inválido</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; }
  </style>
</head>
<body>
  <div class="container py-5 text-center px-4">
    <div class="text-warning mb-3" style="font-size:5rem;">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h4 class="font-weight-bold mb-2">Enlace inválido o expirado</h4>
    <p class="text-muted">
      {{ $message ?? 'Este enlace de firma no es válido, ya fue utilizado o ha expirado (validez: 24 horas).' }}
    </p>
    <p class="text-muted small">
      Solicita un nuevo enlace desde la plataforma de escritorio.
    </p>
  </div>
</body>
</html>
