<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function plans()
    {
        return $this->hasMany(QualityPlan::class, 'department_id');
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            QualityTask::class,
            QualityPlan::class,
            'department_id', // Foreign key on quality_plans
            'plan_id',       // Foreign key on quality_tasks
            'id',            // Local key on departments
            'id'             // Local key on quality_plans
        );
    }
}