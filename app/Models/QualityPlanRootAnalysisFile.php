<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityPlanRootAnalysisFile extends Model
{
    protected $table = 'quality_plan_root_analysis_files';

    protected $fillable = [
        'root_analysis_id',
        'original_name',
        'path',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(QualityPlanRootAnalysis::class, 'root_analysis_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}