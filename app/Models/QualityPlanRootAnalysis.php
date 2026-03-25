<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualityPlanRootAnalysis extends Model
{
    protected $table = 'quality_plan_root_analyses';

    protected $fillable = [
        'plan_id',
        'analysis_description',
        'analysis_team',
        'comments',
    ];

    protected $casts = [
        'analysis_team' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(QualityPlan::class, 'plan_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(QualityPlanRootAnalysisFile::class, 'root_analysis_id');
    }
}