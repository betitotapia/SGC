<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; padding: 30px 35px; }

    h1  { font-size: 17px; text-align: center; color: #1a3a5c; margin-bottom: 3px; }
    .subtitle { text-align: center; font-size: 10px; color: #666; margin-bottom: 18px; }

    .divider { border: none; border-top: 2px solid #1a3a5c; margin: 12px 0; }
    .divider-thin { border: none; border-top: 1px solid #ddd; margin: 10px 0; }

    /* Info table */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .info-table td { padding: 4px 6px; vertical-align: top; }
    .info-table .lbl { font-weight: bold; color: #444; width: 130px; white-space: nowrap; }
    .info-table .val { color: #111; }

    /* Section header */
    .section-title {
      font-size: 11px; font-weight: bold; color: #fff;
      background-color: #1a3a5c; padding: 5px 10px;
      margin-bottom: 0; letter-spacing: 0.4px; text-transform: uppercase;
    }

    /* Signatures table */
    .sigs-table { width: 100%; border-collapse: collapse; }
    .sigs-table th {
      background-color: #2d5986; color: #fff;
      padding: 6px 8px; text-align: left; font-size: 10px;
      border: 1px solid #1a3a5c;
    }
    .sigs-table td {
      padding: 10px 8px; border: 1px solid #ccc; vertical-align: middle;
      font-size: 10px;
    }
    .sig-cell { text-align: center; min-height: 95px; }
    .sigs-table tr.even { background-color: #f5f8fc; }

    .sig-img { max-height: 90px; max-width: 220px; display: block; margin: 0 auto; }
    .no-sig  { color: #aaa; font-style: italic; font-size: 10px; }
    .comments-text { font-size: 9px; color: #555; font-style: italic; margin-top: 3px; }

    /* Status badges */
    .badge { display: inline-block; padding: 2px 7px; border-radius: 3px; font-size: 9px; font-weight: bold; }
    .badge-approved { background: #d4edda; color: #155724; }
    .badge-rejected { background: #f8d7da; color: #721c24; }
    .badge-pending  { background: #fff3cd; color: #856404; }

    /* Footer */
    .footer {
      margin-top: 20px; font-size: 9px; color: #888;
      border-top: 1px solid #ddd; padding-top: 8px; line-height: 1.5;
    }
  </style>
</head>
<body>

  {{-- Encabezado --}}
  <h1>Constancia de Firmas Digitales</h1>
  <p class="subtitle">Sistema de Gestión de Calidad — Control Documental</p>
  <hr class="divider">

  {{-- Datos del documento --}}
  <table class="info-table">
    <tr>
      <td class="lbl">Folio:</td>
      <td class="val"><strong>{{ $version->document->folio }}</strong></td>
      <td class="lbl">Versión:</td>
      <td class="val"><strong>v{{ $version->version_number }}</strong></td>
    </tr>
    <tr>
      <td class="lbl">Título:</td>
      <td class="val" colspan="3">{{ $version->document->title }}</td>
    </tr>
    <tr>
      <td class="lbl">Tipo de documento:</td>
      <td class="val">{{ $version->document->typeLabel() }}</td>
      <td class="lbl">Departamento:</td>
      <td class="val">{{ optional($version->document->department)->name ?? '—' }}</td>
    </tr>
    <tr>
      <td class="lbl">Enviado a revisión:</td>
      <td class="val">{{ optional($version->submitted_at)->format('d/m/Y H:i') ?? '—' }}</td>
      <td class="lbl">Fecha de publicación:</td>
      <td class="val">{{ optional($version->approved_at)->format('d/m/Y H:i') ?? '—' }}</td>
    </tr>
    @if($version->change_reason)
    <tr>
      <td class="lbl">Motivo del cambio:</td>
      <td class="val" colspan="3">{{ $version->change_reason }}</td>
    </tr>
    @endif
  </table>

  <hr class="divider">

  {{-- Tabla de firmas --}}
  <div class="section-title">Registro de firmas autógrafas</div>

  <table class="sigs-table">
    <thead>
      <tr>
        <th style="width:28px; text-align:center;">#</th>
        <th style="width:80px;">Rol</th>
        <th style="width:155px;">Firmante</th>
        <th style="width:105px;">Fecha y hora</th>
        <th style="width:72px;">Estado</th>
        <th>Firma autógrafa</th>
      </tr>
    </thead>
    <tbody>
      @foreach($version->approvals->sortBy('order') as $i => $approval)
        @php
          // Embed signature image as base64 for dompdf
          $imgSrc = null;
          if ($approval->signature_image) {
              $path = storage_path('app/' . $approval->signature_image);
              if (file_exists($path)) {
                  $imgSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
              }
          }
          $badgeClass  = match($approval->status) { 'approved' => 'badge-approved', 'rejected' => 'badge-rejected', default => 'badge-pending' };
          $statusLabel = match($approval->status) { 'approved' => 'Aprobado', 'rejected' => 'Rechazado', default => 'Pendiente' };
          $rowClass    = ($i % 2 === 0) ? '' : 'even';
        @endphp
        <tr class="{{ $rowClass }}">
          <td style="text-align:center; font-weight:bold;">{{ $approval->order }}</td>
          <td><strong>{{ \App\Models\DocumentApproval::ROLE_LABELS[$approval->role_in_approval] ?? '—' }}</strong></td>
          <td>
            {{ optional($approval->user)->name ?? '—' }}
            @if(optional($approval->user)->email)
              <br><span style="color:#777; font-size:9px;">{{ $approval->user->email }}</span>
            @endif
          </td>
          <td>{{ optional($approval->signed_at)->format('d/m/Y H:i:s') ?? '—' }}</td>
          <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
          <td class="sig-cell">
            @if($imgSrc)
              <img src="{{ $imgSrc }}" class="sig-img">
            @elseif($approval->status === 'approved')
              <span class="no-sig">Firmado (sin imagen)</span>
            @else
              <span class="no-sig">—</span>
            @endif
            @if($approval->comments)
              <div class="comments-text">"{{ $approval->comments }}"</div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Pie de página --}}
  <div class="footer">
    Constancia generada el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}.
    Las firmas aquí contenidas corresponden al proceso de revisión y autorización del documento listado arriba,
    conforme al Sistema de Gestión de Calidad. Este documento tiene validez como registro de aprobación.
  </div>

</body>
</html>
