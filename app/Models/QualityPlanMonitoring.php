<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityPlanMonitoring extends Model
{
    protected $table = 'quality_plan_monitorings';

    protected $fillable = [
        'plan_id',
        'period',
        'activity_to_monitor',
        'responsible_name',
        'is_effective',
        'target_goal',
        'goal_percentage',
        'action_close_date',
        'final_result',
    ];

    protected $casts = [
        'is_effective' => 'boolean',
        'action_close_date' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(QualityPlan::class, 'plan_id');
    }
}

