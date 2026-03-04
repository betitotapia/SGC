<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityTaskEvidence extends Model
{
    protected $table = 'quality_task_evidences';

    protected $fillable = [
        'task_id',
        'original_name',
        'path',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(QualityTask::class, 'task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
