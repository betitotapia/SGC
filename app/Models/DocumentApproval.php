<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentApproval extends Model
{
    protected $table = 'document_approvals';

    public const ROLE_AUTHOR   = 'author';
    public const ROLE_REVIEWER = 'reviewer';
    public const ROLE_APPROVER = 'approver';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const ROLE_LABELS = [
        'author'   => 'Elaboró',
        'reviewer' => 'Revisó',
        'approver' => 'Autorizó',
    ];

    protected $fillable = [
        'document_version_id',
        'user_id',
        'role_in_approval',
        'cargo',
        'order',
        'status',
        'signed_at',
        'comments',
        'ip_address',
        'signature_image',
        'mobile_token',
        'mobile_token_expires_at',
    ];

    protected $casts = [
        'signed_at'               => 'datetime',
        'mobile_token_expires_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function version(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'document_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role_in_approval] ?? $this->role_in_approval;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Registra la firma y actualiza el estado de la versión y el documento.
     * Devuelve true si el documento fue publicado (todas las firmas completadas).
     */
    public function approve(?string $comments, ?string $ipAddress): bool
    {
        $this->update([
            'status'     => self::STATUS_APPROVED,
            'signed_at'  => now(),
            'comments'   => $comments,
            'ip_address' => $ipAddress,
        ]);

        // Recargar relaciones desde BD para obtener estado actualizado
        $version  = $this->version()->with('approvals')->first();
        $document = $version->document;

        $pending = $version->approvals()->where('status', self::STATUS_PENDING)->get();

        if ($pending->isEmpty()) {
            // Todas las firmas completadas → publicar
            $version->update([
                'status'      => 'approved',
                'approved_at' => now(),
            ]);

            // Versión anterior pasa a superseded
            if ($document->current_version_id && $document->current_version_id !== $version->id) {
                DocumentVersion::where('id', $document->current_version_id)
                    ->update(['status' => 'superseded']);
            }

            $document->update([
                'status'             => Document::STATUS_PUBLISHED,
                'current_version_id' => $version->id,
            ]);

            return true;
        }

        // Aún hay firmas pendientes — determinar si pasamos a "en aprobación"
        $hasReviewerPending = $pending->contains('role_in_approval', self::ROLE_REVIEWER);
        $newDocStatus     = $hasReviewerPending ? Document::STATUS_IN_REVIEW : Document::STATUS_IN_APPROVAL;
        $newVersionStatus = $hasReviewerPending ? 'in_review' : 'in_approval';

        $version->update(['status' => $newVersionStatus]);
        $document->update(['status' => $newDocStatus]);

        return false;
    }

    /**
     * Rechaza la firma y regresa el documento a borrador.
     */
    public function reject(string $comments, ?string $ipAddress): void
    {
        $this->update([
            'status'     => self::STATUS_REJECTED,
            'signed_at'  => now(),
            'comments'   => $comments,
            'ip_address' => $ipAddress,
        ]);

        $version  = $this->version()->first();
        $document = $version->document;

        $version->update(['status' => 'draft']);
        $document->update(['status' => Document::STATUS_DRAFT]);
    }
}
