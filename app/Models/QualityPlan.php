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
            'status',
            'origin',
            'finding_type',
            'detected_by',
            'auditor_type',
            'department_id',
            'owner_id',
            'owner_name',
            'owner_email',
            'process',
            'finding',
            'notes',
            'open_date',
            'commitment_date',
            'close_date',
            'progress',
            'final_result',
            'final_result_by',
            'final_result_at',
        ];

    protected $casts = [
            'open_date' => 'date',
            'commitment_date' => 'date',
            'close_date' => 'date',
            'final_result_at' => 'datetime',
        ];

    public function tasks(): HasMany
            {
                return $this->hasMany(QualityTask::class, 'plan_id')
                    ->orderBy('sort_order')
                    ->orderBy('id');
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
    public function monitorings()
        {
            return $this->hasMany(\App\Models\QualityPlanMonitoring::class, 'plan_id')->latest();
        }

    public function finalResultUser(): BelongsTo
        {
            return $this->belongsTo(\App\Models\User::class, 'final_result_by');
        }

        public function rootAnalyses()
        {
            return $this->hasMany(\App\Models\QualityPlanRootAnalysis::class, 'plan_id')->latest();
        }
}