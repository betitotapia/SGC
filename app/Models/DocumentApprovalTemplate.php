<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentApprovalTemplate extends Model
{
    protected $table = 'document_approval_templates';

    protected $fillable = [
        'department_id',
        'document_type',
        'user_id',
        'role_in_approval',
        'order',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Retorna las plantillas aplicables a un tipo de documento y departamento dado.
     * Primero las específicas del tipo, luego las genéricas (null).
     */
    public function scopeForDocument($query, int $departmentId, string $documentType)
    {
        return $query->where('department_id', $departmentId)
            ->where(function ($q) use ($documentType) {
                $q->where('document_type', $documentType)
                  ->orWhereNull('document_type');
            })
            ->orderByRaw("CASE WHEN document_type IS NULL THEN 1 ELSE 0 END")
            ->orderBy('order');
    }
}
