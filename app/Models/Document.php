<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    public const TYPE_PROCEDURE        = 'procedure';
    public const TYPE_PROCESS          = 'process';
    public const TYPE_FORMAT           = 'format';
    public const TYPE_WORK_INSTRUCTION = 'work_instruction';

    public const STATUS_DRAFT       = 'draft';
    public const STATUS_IN_REVIEW   = 'in_review';
    public const STATUS_IN_APPROVAL = 'in_approval';
    public const STATUS_PUBLISHED   = 'published';
    public const STATUS_OBSOLETE    = 'obsolete';

    public const TYPE_LABELS = [
        'procedure'        => 'Procedimiento',
        'process'          => 'Proceso',
        'format'           => 'Formato',
        'work_instruction' => 'Instrucción de Trabajo',
    ];

    public const TYPE_CODES = [
        'procedure'        => 'PRO',
        'process'          => 'PROC',
        'format'           => 'FOR',
        'work_instruction' => 'IT',
    ];

    public const STATUS_LABELS = [
        'draft'       => 'Borrador',
        'in_review'   => 'En Revisión',
        'in_approval' => 'En Aprobación',
        'published'   => 'Publicado',
        'obsolete'    => 'Obsoleto',
    ];

    protected $fillable = [
        'folio',
        'title',
        'type',
        'department_id',
        'current_version_id',
        'status',
        'created_by',
        'elaboro_id',
        'elaboro_cargo',
        'reviso_id',
        'reviso_cargo',
        'autorizo_id',
        'autorizo_cargo',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function elaboro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'elaboro_id');
    }

    public function reviso(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviso_id');
    }

    public function autorizo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizo_id');
    }

    /**
     * Devuelve los tres firmantes como array ordenado para crear document_approvals.
     */
    public function signers(): array
    {
        return array_filter([
            ['user_id' => $this->elaboro_id,  'role_in_approval' => DocumentApproval::ROLE_AUTHOR,   'order' => 1, 'cargo' => $this->elaboro_cargo],
            ['user_id' => $this->reviso_id,   'role_in_approval' => DocumentApproval::ROLE_REVIEWER, 'order' => 2, 'cargo' => $this->reviso_cargo],
            ['user_id' => $this->autorizo_id, 'role_in_approval' => DocumentApproval::ROLE_APPROVER, 'order' => 3, 'cargo' => $this->autorizo_cargo],
        ], fn ($s) => ! empty($s['user_id']));
    }

    public function hasSigners(): bool
    {
        return $this->elaboro_id || $this->reviso_id || $this->autorizo_id;
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->orderByDesc('id');
    }

    public function latestVersion(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->latest();
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Genera el folio automático: {TIPO_CODE}-{DEPT_CODE}-{###}
     * Ej: PRO-RH-001
     */
    public static function generateFolio(string $type, Department $department): string
    {
        $typeCode = self::TYPE_CODES[$type] ?? strtoupper($type);
        $deptCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $department->name), 0, 4));

        $last = self::where('type', $type)
            ->where('department_id', $department->id)
            ->count();

        $consecutive = str_pad($last + 1, 3, '0', STR_PAD_LEFT);

        return "{$typeCode}-{$deptCode}-{$consecutive}";
    }

    /**
     * Calcula el próximo número de versión.
     * Si no hay versiones: 1.0. Si la revisión es mayor (nuevo ciclo): N.0. Si es menor: N.M+1
     */
    public function nextVersionNumber(bool $majorRevision = false): string
    {
        $latest = $this->versions()->first();

        if (! $latest) {
            return '1.0';
        }

        [$major, $minor] = explode('.', $latest->version_number) + [0, 0];

        if ($majorRevision) {
            return ($major + 1) . '.0';
        }

        return $major . '.' . ($minor + 1);
    }
}
