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

class DocumentMobileSignController extends Controller
{
    /**
     * Genera (o reutiliza) el token de firma móvil.
     * Requiere auth — se llama desde la interfaz de escritorio.
     */
    public function generateToken(Request $request, Document $document, DocumentApproval $approval): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if ($approval->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        if (! $approval->isPending()) {
            return response()->json(['error' => 'Esta firma ya fue procesada.'], 422);
        }

        // Reutilizar token si sigue vigente, de lo contrario generar uno nuevo
        if (! $approval->mobile_token || $approval->mobile_token_expires_at?->isPast()) {
            $token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
            $approval->update([
                'mobile_token'            => $token,
                'mobile_token_expires_at' => now()->addHours(24),
            ]);
        }

        return response()->json([
            'url'        => route('quality.mobile-sign.show', $approval->mobile_token),
            'expires_at' => $approval->mobile_token_expires_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Muestra la página de firma móvil (sin autenticación — el token es la credencial).
     */
    public function show(string $token): View|\Illuminate\Http\Response
    {
        $approval = DocumentApproval::where('mobile_token', $token)
            ->where('mobile_token_expires_at', '>', now())
            ->where('status', 'pending')
            ->with(['version.approvals.user', 'version.document.department', 'user'])
            ->first();

        if (! $approval) {
            return response()->view('quality.documents.mobile-sign-invalid', [], 410);
        }

        $version  = $approval->version;
        $document = $version->document;

        $next = $version->nextPendingApproval();
        if (! $next || $next->id !== $approval->id) {
            return response()->view('quality.documents.mobile-sign-invalid', [
                'message' => 'Aún no es tu turno de firmar o la firma ya fue procesada.',
            ], 422);
        }

        return view('quality.documents.mobile-sign', compact('approval', 'version', 'document', 'token'));
    }

    /**
     * Procesa la firma enviada desde el dispositivo móvil.
     */
    public function store(Request $request, string $token): View|RedirectResponse
    {
        $approval = DocumentApproval::where('mobile_token', $token)
            ->where('mobile_token_expires_at', '>', now())
            ->where('status', 'pending')
            ->with(['version.approvals.user', 'version.document.department', 'user'])
            ->first();

        if (! $approval) {
            return response()->view('quality.documents.mobile-sign-invalid', [], 410);
        }

        $version  = $approval->version;
        $document = $version->document;

        $next = $version->nextPendingApproval();
        if (! $next || $next->id !== $approval->id) {
            return response()->view('quality.documents.mobile-sign-invalid', [
                'message' => 'Ya no es tu turno de firmar.',
            ], 422);
        }

        $action = $request->input('action', 'approve');

        if ($action === 'reject') {
            $request->validate([
                'comments' => ['required', 'string', 'min:10', 'max:1000'],
            ], [
                'comments.required' => 'El motivo del rechazo es obligatorio (mínimo 10 caracteres).',
                'comments.min'      => 'El motivo debe tener al menos 10 caracteres.',
            ]);

            $approval->reject($request->input('comments'), $request->ip());
            $approval->update(['mobile_token' => null, 'mobile_token_expires_at' => null]);

            $document->refresh();
            DocumentNotifier::rejected($document, $approval);

            \App\Support\Audit::log('document_rejected', $document, null, [
                'version'     => $version->version_number,
                'rejected_by' => $approval->user_id,
                'channel'     => 'mobile',
            ]);

            return view('quality.documents.mobile-sign-done', [
                'success'   => false,
                'message'   => 'Documento rechazado. El autor será notificado.',
                'published' => false,
            ]);
        }

        // ── Aprobación ────────────────────────────────────────────────────────
        $request->validate([
            'signature_data' => ['required', 'string'],
            'confirmed'      => ['required', 'accepted'],
        ], [
            'confirmed.required'      => 'Debes confirmar que revisaste el documento.',
            'confirmed.accepted'      => 'Debes confirmar que revisaste el documento.',
            'signature_data.required' => 'Debes dibujar tu firma antes de aprobar.',
        ]);

        $sigPath = $this->storeSignatureImage($request->input('signature_data'), $approval->id);
        if ($sigPath) {
            $approval->update(['signature_image' => $sigPath]);
        }

        $published = $approval->approve($request->input('comments'), $request->ip());

        // Invalidar token (ya fue utilizado)
        $approval->update(['mobile_token' => null, 'mobile_token_expires_at' => null]);

        $document->refresh();

        if ($published) {
            $freshVersion = $approval->version()->with('approvals.user', 'document.department')->first();
            DocumentCertificate::generate($freshVersion);

            try {
                \App\Support\SignedPdfBuilder::build($freshVersion);
            } catch (\Throwable $e) {
                \Log::error('SignedPdfBuilder (mobile): ' . $e->getMessage());
            }

            $document->load('currentVersion');
            DocumentNotifier::published($document);

            \App\Support\Audit::log('document_published', $document, null, [
                'version' => $document->currentVersion?->version_number,
                'channel' => 'mobile',
            ]);
        } else {
            $freshVersion = $approval->version()->with('approvals')->first();
            $nextApproval = $freshVersion->nextPendingApproval();
            if ($nextApproval) {
                DocumentNotifier::nextSignerNotified($document, $nextApproval);
            }

            \App\Support\Audit::log('document_signed', $document, null, [
                'version' => $version->version_number,
                'role'    => $approval->role_in_approval,
                'channel' => 'mobile',
            ]);
        }

        return view('quality.documents.mobile-sign-done', [
            'success'   => true,
            'message'   => '¡Firma registrada exitosamente!',
            'published' => $published,
        ]);
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
}
