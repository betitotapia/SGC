<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
     protected $fillable = ['plan_id','titulo','descripcion','fecha_compromiso','estatus','cerrada_en'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function evidences()
    {
        return $this->hasMany(TaskEvidence::class);
    }
}
