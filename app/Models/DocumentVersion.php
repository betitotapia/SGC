<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentVersion extends Model
{
    protected $table = 'document_versions';

    protected $fillable = [
        'document_id',
        'version_number',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'change_reason',
        'effective_date',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_at',
        'certificate_path',
        'signed_pdf_path',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'submitted_at'   => 'datetime',
        'approved_at'    => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class, 'document_version_id')->orderBy('order');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    /**
     * Devuelve la primera firma pendiente en orden, o null si todas han firmado.
     */
    public function nextPendingApproval(): ?DocumentApproval
    {
        return $this->approvals()
            ->where('status', DocumentApproval::STATUS_PENDING)
            ->first();
    }

    public function allApproved(): bool
    {
        return $this->approvals()->where('status', '!=', DocumentApproval::STATUS_APPROVED)->doesntExist();
    }

    public function hasRejection(): bool
    {
        return $this->approvals()->where('status', DocumentApproval::STATUS_REJECTED)->exists();
    }

    /**
     * Copia las plantillas de aprobación del departamento para esta versión.
     */
    public function loadApprovalsFromTemplates(): void
    {
        $document = $this->document;

        $templates = DocumentApprovalTemplate::where('department_id', $document->department_id)
            ->where(function ($q) use ($document) {
                $q->where('document_type', $document->type)
                  ->orWhereNull('document_type');
            })
            ->orderBy('order')
            ->get();

        foreach ($templates as $template) {
            $this->approvals()->create([
                'user_id'          => $template->user_id,
                'role_in_approval' => $template->role_in_approval,
                'order'            => $template->order,
                'status'           => DocumentApproval::STATUS_PENDING,
            ]);
        }
    }
}
