<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentVersionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:documents.update'])->only(['store', 'destroy']);
    }

    /**
     * Crea una nueva versión (borrador) del documento y sube el archivo.
     * Aplica tanto al primer upload como a revisiones posteriores.
     */
    public function store(Request $request, Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        // Solo se puede subir versión si el documento está en draft o publicado
        if (! in_array($document->status, [Document::STATUS_DRAFT, Document::STATUS_PUBLISHED])) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'No se puede crear una nueva versión mientras hay un proceso de firma activo.');
        }

        // No puede haber ya una versión en draft
        $existingDraft = $document->versions()->where('status', 'draft')->exists();
        if ($existingDraft) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Ya existe una versión en borrador. Envíala a revisión o elimínala antes de crear una nueva.');
        }

        $data = $request->validate([
            'file'           => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', 'max:20480'],
            'change_reason'  => ['nullable', 'string', 'max:1000'],
            'effective_date' => ['nullable', 'date'],
        ]);

        $file         = $request->file('file');
        $versionNumber = $document->nextVersionNumber();
        $storedPath   = $file->store("documents/{$document->id}", 'public_ftp');

        DocumentVersion::create([
            'document_id'    => $document->id,
            'version_number' => $versionNumber,
            'file_path'      => $storedPath,
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getMimeType(),
            'size_bytes'     => $file->getSize(),
            'change_reason'  => $data['change_reason'] ?? null,
            'effective_date' => $data['effective_date'] ?? null,
            'status'         => 'draft',
            'submitted_by'   => $request->user()->id,
        ]);

        // Si el documento estaba publicado, lo marcamos como draft para indicar revisión en curso
        if ($document->status === Document::STATUS_PUBLISHED) {
            $document->update(['status' => Document::STATUS_DRAFT]);
        }

        \App\Support\Audit::log('document_version_uploaded', $document, null, ['version' => $versionNumber]);

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', "Versión {$versionNumber} subida correctamente. Ahora puedes enviarla a revisión.");
    }

    /**
     * Elimina una versión borrador y su archivo.
     */
    public function destroy(Document $document, DocumentVersion $version): RedirectResponse
    {
        $this->authorizeAccess($document);

        if ($version->status !== 'draft') {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Solo se pueden eliminar versiones en borrador.');
        }

        if ($version->file_path) {
            Storage::disk('public_ftp')->delete($version->file_path);
        }

        $version->delete();

        // Si el documento no tiene más versiones draft y tenía una publicada, restaurar status
        $hasCurrent = $document->current_version_id !== null;
        if ($hasCurrent) {
            $document->update(['status' => Document::STATUS_PUBLISHED]);
        }

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', 'Versión eliminada.');
    }

    /**
     * Descarga el archivo del documento (versión específica).
     * Para versiones publicadas con PDF firmado disponible, sirve el PDF firmado.
     */
    public function download(Document $document, DocumentVersion $version): Response|RedirectResponse
    {
        $this->authorizeAccess($document);

        // Servir el PDF firmado (con cadena de firmas embebida) si está disponible
        if (
            $version->signed_pdf_path
            && Storage::disk('local')->exists($version->signed_pdf_path)
        ) {
            $baseName = pathinfo($version->original_name ?? 'documento', PATHINFO_FILENAME);
            $filename = $baseName . '_firmado.pdf';

            return response(
                Storage::disk('local')->get($version->signed_pdf_path),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]
            );
        }

        if (! $version->file_path || ! Storage::disk('public_ftp')->exists($version->file_path)) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'El archivo no fue encontrado en el servidor.');
        }

        $filename = $version->original_name ?? 'documento';

        return response(
            Storage::disk('public_ftp')->get($version->file_path),
            200,
            [
                'Content-Type'        => $version->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * Descarga la constancia de firmas PDF de una versión publicada.
     */
    public function downloadCertificate(Document $document, DocumentVersion $version): Response|RedirectResponse
    {
        $this->authorizeAccess($document);

        if (! $version->certificate_path || ! Storage::disk('local')->exists($version->certificate_path)) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'La constancia de firmas aún no está disponible para esta versión.');
        }

        $filename = "Constancia_{$document->folio}_v{$version->version_number}.pdf";

        return response(
            Storage::disk('local')->get($version->certificate_path),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    protected function authorizeAccess(Document $document): void
    {
        $user = request()->user();

        if (! $user->can('documents.view_all') && $document->department_id !== $user->department_id) {
            abort(403);
        }
    }
}
