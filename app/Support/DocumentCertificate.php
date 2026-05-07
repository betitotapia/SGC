<?php

namespace App\Support;

use App\Models\DocumentVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentCertificate
{
    public static function generate(DocumentVersion $version): string
    {
        $version->load('approvals.user', 'document.department');

        $pdf = Pdf::loadView('quality.documents.certificate', compact('version'));
        $pdf->setPaper('letter', 'portrait');

        $safeForlio  = preg_replace('/[^A-Za-z0-9\-]/', '_', $version->document->folio);
        $safeVersion = str_replace('.', '-', $version->version_number);
        $filename    = "certificates/cert_{$safeForlio}_v{$safeVersion}.pdf";

        Storage::disk('local')->put($filename, $pdf->output());

        $version->update(['certificate_path' => $filename]);

        return $filename;
    }
}
