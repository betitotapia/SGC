<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityPlan extends Model
{
    protected $table = 'quality_plans';

    protected $fillable = [
        'folio',
        'open_date',
        'origin',
        'process',
        'finding_type',
        'detected_by',
        'auditor_type',
        'finding',
        'activity',
        'root_cause',
        'department_id',
        'owner_name',
        'owner_id',
        'commitment_date',
        'close_date',
        'status',
        'progress',
        'notes',
    ];

    protected $casts = [
        'open_date'        => 'date',
        'commitment_date'  => 'date',
        'close_date'       => 'date',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(QualityTask::class, 'plan_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function recalcProgress(): void
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            $this->progress = 0;
            $this->save();
            return;
        }

        $closed = $this->tasks()->where('status', QualityTask::STATUS_CLOSED)->count();
        $progress = (int) round(($closed / $total) * 100);

        $this->progress = max(0, min(100, $progress));
        $this->save();
    }
}