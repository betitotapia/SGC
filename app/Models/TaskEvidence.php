<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskEvidence extends Model
{
    protected $fillable = ['task_id','nombre_archivo','ruta_archivo','tipo_archivo'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
