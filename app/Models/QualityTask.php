<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualityTask extends Model
{
    protected $table = 'quality_tasks';

    public const STATUS_OPEN        = 'ABIERTA';
    public const STATUS_IN_PROGRESS = 'EN_PROCESO';
    public const STATUS_CLOSED      = 'CERRADA';

    protected $fillable = [
        'plan_id',
        'title',
        'description',
        'commitment_date',
        'status',
        'closed_at',
        'assignee_id',
    ];

    protected $casts = [
        'commitment_date' => 'date',
        'closed_at'       => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(QualityPlan::class, 'plan_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(QualityTaskEvidence::class, 'task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function markClosed(): void
    {
        $this->status = self::STATUS_CLOSED;
        $this->closed_at = now();
        $this->save();
    }

    public function markOpen(): void
    {
        $this->closed_at = null;
        $this->status = self::STATUS_OPEN;
        $this->save();
    }
}
