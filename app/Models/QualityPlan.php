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
        'process',
        'finding_type',
        'finding',
        'activity',
        'root_cause',
        'department',
        'owner_name',
        'owner_id',
        'commitment_date',
        'close_date',
        // MANUAL
        'status',
        // AUTO
        'progress',
        'notes',
    ];

    protected $casts = [
       'open_date'      => 'date',
        'commitment_date'=> 'date',
        'close_date'     => 'date',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(QualityTask::class, 'plan_id');
    }

    public function owner(): BelongsTo
    {
          return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    /** Recalcula el progreso (0..100) por tareas cerradas. NO toca el status del plan (manual). */
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
    public function department()
        {
            return $this->belongsTo(\App\Models\Department::class, 'department_id');
        }
    
}
