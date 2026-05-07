<?php

namespace App\Support;

use App\Models\DocumentVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;

class SignedPdfBuilder
{
    /**
     * Genera el PDF firmado: documento original + página de cadena de firmas.
     * Almacena el resultado en disco local y actualiza signed_pdf_path en la versión.
     */
    public static function build(DocumentVersion $version): string
    {
        $version->loadMissing('approvals.user', 'document.department');

        // 1. Generar la página de firmas como PDF con DomPDF
        $sigPdf     = Pdf::loadView('quality.documents.signature-page', compact('version'));
        $sigPdf->setPaper('letter', 'portrait');
        $sigContent = $sigPdf->output();

        $tempSig = sys_get_temp_dir() . '/sig_' . $version->id . '_' . uniqid() . '.pdf';
        file_put_contents($tempSig, $sigContent);

        $merged = null;

        try {
            // 2. Intentar combinar con el PDF original
            if ($version->file_path && Storage::disk('public_ftp')->exists($version->file_path)) {
                $origContent = Storage::disk('public_ftp')->get($version->file_path);
                $tempOrig    = sys_get_temp_dir() . '/orig_' . $version->id . '_' . uniqid() . '.pdf';
                file_put_contents($tempOrig, $origContent);

                try {
                    $merged = self::mergePdfs($tempOrig, $tempSig);
                } finally {
                    @unlink($tempOrig);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('SignedPdfBuilder: no se pudo combinar el PDF original — ' . $e->getMessage());
        } finally {
            @unlink($tempSig);
        }

        // Si no se pudo combinar, guardar solo la página de firmas
        $finalContent = $merged ?? $sigContent;

        // 3. Guardar en disco local (privado)
        $document    = $version->document;
        $safeForlio  = preg_replace('/[^A-Za-z0-9\-]/', '_', $document->folio);
        $safeVersion = str_replace('.', '-', $version->version_number);
        $filename    = "documents/signed/signed_{$safeForlio}_v{$safeVersion}.pdf";

        Storage::disk('local')->put($filename, $finalContent);
        $version->update(['signed_pdf_path' => $filename]);

        return $filename;
    }

    /**
     * Combina el PDF original con la página de firmas usando FPDI + TCPDF.
     */
    private static function mergePdfs(string $origPath, string $sigPath): string
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        // Importar páginas del original
        $pageCount = $pdf->setSourceFile($origPath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl  = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tpl);

            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height'], true);
        }

        // Importar la página de firmas
        $pdf->setSourceFile($sigPath);
        $tpl  = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->AddPage('P', [$size['width'], $size['height']]);
        $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height'], true);

        return $pdf->Output('', 'S');
    }
}
