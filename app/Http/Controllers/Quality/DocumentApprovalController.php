<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Support\DocumentCertificate;
use App\Support\DocumentNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Envía la versión borrador al flujo de firmas usando los firmantes definidos en el documento.
     */
    public function submit(Request $request, Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        if ($document->status !== Document::STATUS_DRAFT) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Solo se pueden enviar a revisión documentos en borrador.');
        }

        $draftVersion = $document->versions()->where('status', 'draft')->first();

        if (! $draftVersion) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'No hay ninguna versión en borrador. Sube el archivo primero.');
        }

        if (! $document->hasSigners()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'El documento no tiene firmantes definidos. Edítalo y asigna al menos un firmante antes de enviar a revisión.');
        }

        // Eliminar aprobaciones previas (si es un reenvío después de rechazo)
        $draftVersion->approvals()->delete();

        // Crear las aprobaciones desde los firmantes del documento
        foreach ($document->signers() as $signer) {
            $draftVersion->approvals()->create([
                'user_id'          => $signer['user_id'],
                'role_in_approval' => $signer['role_in_approval'],
                'cargo'            => $signer['cargo'] ?? null,
                'order'            => $signer['order'],
                'status'           => DocumentApproval::STATUS_PENDING,
            ]);
        }

        $draftVersion->update([
            'status'       => 'in_review',
            'submitted_by' => $request->user()->id,
            'submitted_at' => now(),
        ]);

        $document->update(['status' => Document::STATUS_IN_REVIEW]);

        // Notificar al primer firmante
        $firstApproval = $draftVersion->approvals()->orderBy('order')->first();
        if ($firstApproval) {
            DocumentNotifier::submitted($document, $firstApproval);
        }

        \App\Support\Audit::log('document_submitted', $document, null, ['version' => $draftVersion->version_number]);

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', 'Documento enviado a revisión. Se notificó al primer firmante.');
    }

    /**
     * Endpoint ligero: devuelve si la aprobación sigue pendiente.
     * Usado por el polling de sign.blade.php después de generar el QR.
     */
    public function checkPending(Document $document, DocumentApproval $approval): \Illuminate\Http\JsonResponse
    {
        if ($approval->user_id !== request()->user()->id) {
            return response()->json(['pending' => false]);
        }

        return response()->json(['pending' => $approval->fresh()->isPending()]);
    }

    /**
     * Lista todas las firmas pendientes del usuario autenticado.
     */
    public function pending(Request $request): View
    {
        $user = $request->user();

        $pendingApprovals = DocumentApproval::with([
                'version.document.department',
                'version.approvals',
            ])
            ->where('user_id', $user->id)
            ->where('status', DocumentApproval::STATUS_PENDING)
            ->whereHas('version', fn ($q) =>
                $q->whereIn('status', ['in_review', 'in_approval'])
            )
            ->orderBy('created_at')
            ->get()
            ->filter(fn ($approval) =>
                optional($approval->version)->nextPendingApproval()?->id === $approval->id
            );

        return view('quality.documents.pending', compact('pendingApprovals'));
    }

    /**
     * Muestra la página de firma digital dedicada.
     */
    public function showSign(Document $document, DocumentApproval $approval): View|RedirectResponse
    {
        $this->authorizeAccess($document);

        if ($approval->user_id !== request()->user()->id) {
            abort(403, 'Esta firma no te pertenece.');
        }

        if (! $approval->isPending()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Esta firma ya fue procesada.');
        }

        $version = $approval->version()->with('approvals.user', 'document.department')->first();

        return view('quality.documents.sign', compact('document', 'approval', 'version'));
    }

    /**
     * Registra la firma (aprobación o rechazo).
     */
    public function sign(Request $request, Document $document, DocumentApproval $approval): RedirectResponse
    {
        $this->authorizeAccess($document);

        if ($approval->user_id !== $request->user()->id) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'No tienes permiso para firmar este documento.');
        }

        if (! $approval->isPending()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Esta firma ya fue procesada.');
        }

        // Validar que sea el turno de este firmante (orden secuencial)
        $nextPending = $approval->version()->first()->nextPendingApproval();
        if (! $nextPending || $nextPending->id !== $approval->id) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Aún no es tu turno. Espera a que los firmantes anteriores completen su firma.');
        }

        $action   = $request->input('action'); // 'approve' | 'reject'
        $comments = $request->input('comments');

        if ($action === 'reject') {
            $request->validate([
                'comments' => ['required', 'string', 'min:10', 'max:1000'],
            ], ['comments.required' => 'El motivo del rechazo es obligatorio (mínimo 10 caracteres).']);

            $approval->reject($comments, $request->ip());

            $document->refresh();
            DocumentNotifier::rejected($document, $approval);

            \App\Support\Audit::log('document_rejected', $document, null, [
                'version'     => $approval->version?->version_number,
                'rejected_by' => $request->user()->id,
                'comments'    => $comments,
            ]);

            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Documento rechazado. Se notificó al creador con tu motivo.');
        }

        // Validar confirmación + firma dibujada
        $request->validate([
            'confirmed'      => ['accepted'],
            'signature_data' => ['required', 'string'],
        ], [
            'confirmed.accepted'      => 'Debes confirmar que revisaste el documento antes de firmar.',
            'signature_data.required' => 'Debes dibujar tu firma antes de aprobar.',
        ]);

        // Guardar imagen de firma autógrafa
        $signatureData = $request->input('signature_data');
        $signaturePath = $this->storeSignatureImage($signatureData, $approval->id);
        if ($signaturePath) {
            $approval->update(['signature_image' => $signaturePath]);
        }

        $published = $approval->approve($comments, $request->ip());

        $document->refresh();

        if ($published) {
            // Generar constancia de firmas PDF y documento firmado
            $version = $approval->version()->with('approvals.user', 'document.department')->first();
            DocumentCertificate::generate($version);

            try {
                \App\Support\SignedPdfBuilder::build($version);
            } catch (\Throwable $e) {
                \Log::error('SignedPdfBuilder: ' . $e->getMessage());
            }

            $document->load('currentVersion');
            DocumentNotifier::published($document);

            \App\Support\Audit::log('document_published', $document, null, [
                'version' => $document->currentVersion?->version_number,
            ]);

            return redirect()
                ->route('quality.documents.show', $document)
                ->with('ok', '¡Documento publicado! Todas las firmas fueron completadas. La constancia de firmas ya está disponible.');
        }

        // Notificar al siguiente firmante
        $version      = $approval->version()->with('approvals')->first();
        $nextApproval = $version->nextPendingApproval();

        if ($nextApproval) {
            DocumentNotifier::nextSignerNotified($document, $nextApproval);
        }

        \App\Support\Audit::log('document_signed', $document, null, [
            'version'   => $version->version_number,
            'signed_by' => $request->user()->id,
            'role'      => $approval->role_in_approval,
        ]);

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', 'Firma registrada correctamente. Se notificó al siguiente firmante.');
    }

    /**
     * Decodifica la imagen base64 del canvas y la guarda como PNG.
     * Devuelve la ruta relativa dentro del disco local, o null si no hay datos válidos.
     */
    /**
     * Sirve la imagen PNG de la firma autógrafa (almacenada en disco privado).
     */
    public function signatureImage(DocumentApproval $approval): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        // Solo el firmante, su departamento, o quien tenga view_all puede ver la firma
        $user = request()->user();
        $doc  = $approval->version?->document;

        if ($doc && ! $user->can('documents.view_all') && $doc->department_id !== $user->department_id && $approval->user_id !== $user->id) {
            abort(403);
        }

        if (! $approval->signature_image || ! Storage::disk('local')->exists($approval->signature_image)) {
            abort(404);
        }

        return response(
            Storage::disk('local')->get($approval->signature_image),
            200,
            ['Content-Type' => 'image/png']
        );
    }

    private function storeSignatureImage(string $dataUrl, int $approvalId): ?string
    {
        if (! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $base64 = substr($dataUrl, strlen('data:image/png;base64,'));
        $binary = base64_decode($base64, strict: true);

        if ($binary === false || strlen($binary) < 100) {
            return null;
        }

        $path = "signatures/approval_{$approvalId}.png";
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    protected function authorizeAccess(Document $document): void
    {
        $user = request()->user();

        if (! $user->can('documents.view_all') && $document->department_id !== $user->department_id) {
            // Los firmantes pueden ver el documento aunque sean de otro departamento
            $isPendingSigner = \App\Models\DocumentApproval::whereHas('version', fn ($q) =>
                $q->where('document_id', $document->id)
            )->where('user_id', $user->id)->where('status', 'pending')->exists();

            if (! $isPendingSigner) {
                abort(403);
            }
        }
    }
}
