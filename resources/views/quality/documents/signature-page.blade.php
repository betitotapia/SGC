<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    @page {
      margin: 15mm 15mm 15mm 15mm;
      size: letter portrait;
    }
    * { box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      font-size: 10pt;
      color: #000;
      margin: 0;
      padding: 0;
    }

    /* ── Encabezado ── */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    .header-logo {
      width: 90px;
      vertical-align: middle;
      text-align: left;
    }
    .header-title {
      vertical-align: middle;
      text-align: center;
      font-size: 13pt;
      font-weight: bold;
      padding: 0 10px;
      text-transform: uppercase;
    }
    .header-code {
      width: 135px;
      vertical-align: middle;
    }
    .header-code table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      font-size: 8.5pt;
      text-align: center;
    }
    .header-code table td {
      border-bottom: 1px solid #000;
      padding: 3px 5px;
    }
    .header-code table tr:last-child td {
      border-bottom: none;
    }

    /* ── Título de sección ── */
    .section-title {
      text-align: center;
      font-size: 11pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 20px 0 14px;
      border-bottom: 1.5px solid #333;
      padding-bottom: 6px;
    }

    /* ── Tabla de firmas ── */
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .sig-table th {
      border: 1.5px solid #333;
      background-color: #d9d9d9;
      text-align: center;
      padding: 7px 5px;
      font-size: 9.5pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .sig-table td {
      border: 1.5px solid #333;
      text-align: center;
      vertical-align: middle;
      padding: 12px 8px;
      width: 33.33%;
    }
    .sig-image-wrap {
      min-height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 6px;
    }
    .sig-image {
      max-height: 75px;
      max-width: 170px;
    }
    .no-sig {
      color: #aaa;
      font-style: italic;
      font-size: 8pt;
    }
    .sig-name {
      font-weight: bold;
      font-size: 9.5pt;
      margin-top: 4px;
    }
    .sig-cargo {
      font-size: 8.5pt;
      color: #333;
      margin-top: 2px;
    }
    .sig-date {
      font-size: 8pt;
      color: #666;
      margin-top: 3px;
    }

    /* ── Nota al pie ── */
    .footer-note {
      margin-top: 30px;
      font-size: 7.5pt;
      color: #666;
      text-align: center;
      border-top: 1px solid #ccc;
      padding-top: 6px;
    }
  </style>
</head>
<body>

@php
  $document      = $version->document;
  $effectiveDate = $version->effective_date ?? ($version->approved_at ?? now());
  $revLabel      = 'REV. ' . strtoupper($version->version_number);
  $dateLabel     = 'FECHA: ' . (is_string($effectiveDate) ? $effectiveDate : $effectiveDate->format('d/m/Y'));
@endphp

{{-- ── Encabezado ── --}}
<table class="header-table">
  <tr>
    <td class="header-logo">
      @php
        $logoSrc = null;
        foreach (['public/img/logo.png', 'public/images/logo.png', 'public/img/logo.jpg', 'public/logo.png'] as $try) {
          $tryPath = base_path($try);
          if (file_exists($tryPath) && is_readable($tryPath)) {
            $ext     = strtolower(pathinfo($tryPath, PATHINFO_EXTENSION));
            $mime    = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($tryPath));
            break;
          }
        }
      @endphp
      @if($logoSrc)
        <img src="{{ $logoSrc }}" style="max-height:50px; max-width:85px;">
      @else
        <span style="font-weight:bold; font-size:11pt; color:#1a3a5c;">SGC</span>
      @endif
    </td>
    <td class="header-title">{{ $document->title }}</td>
    <td class="header-code">
      <table>
        <tr><td><strong>{{ $document->folio }}</strong></td></tr>
        <tr><td>{{ $revLabel }}</td></tr>
        <tr><td>{{ $dateLabel }}</td></tr>
      </table>
    </td>
  </tr>
</table>

{{-- ── Título sección ── --}}
<div class="section-title">Cadena de Firmas</div>

{{-- ── Tabla de firmas ── --}}
<table class="sig-table">
  <thead>
    <tr>
      @foreach($version->approvals as $approval)
        <th>{{ $approval->roleLabel() }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    <tr>
      @foreach($version->approvals as $approval)
        <td>
          {{-- Imagen de firma (base64 para compatibilidad con DomPDF) --}}
          <div class="sig-image-wrap">
            @if($approval->signature_image)
              @php
                $imgSrc = null;
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($approval->signature_image)) {
                  $imgSrc = 'data:image/png;base64,' . base64_encode(
                    \Illuminate\Support\Facades\Storage::disk('local')->get($approval->signature_image)
                  );
                }
              @endphp
              @if($imgSrc)
                <img src="{{ $imgSrc }}" class="sig-image">
              @else
                <span class="no-sig">Firma no disponible</span>
              @endif
            @else
              <span class="no-sig">Pendiente</span>
            @endif
          </div>

          {{-- Nombre --}}
          <div class="sig-name">{{ optional($approval->user)->name ?? '—' }}</div>

          {{-- Cargo --}}
          @if($approval->cargo)
            <div class="sig-cargo">{{ $approval->cargo }}</div>
          @endif

          {{-- Fecha de firma --}}
          @if($approval->signed_at)
            <div class="sig-date">{{ $approval->signed_at->format('d/m/Y') }}</div>
          @endif
        </td>
      @endforeach
    </tr>
  </tbody>
</table>

<div class="footer-note">
  Documento generado electrónicamente por el Sistema de Gestión de Calidad.
  Folio: {{ $document->folio }} — Versión: {{ $version->version_number }}
</div>

</body>
</html>
