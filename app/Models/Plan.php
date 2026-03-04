<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'folio','proceso','tipo_hallazgo','hallazgo','actividad','causa_raiz',
        'departamento','responsable','fecha_compromiso','fecha_cierre',
        'estatus','progress','observaciones'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function recalcProgress(): void
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            $this->progress = 0;
            $this->estatus = 'ABIERTO';
            $this->save();
            return;
        }

        $closed = $this->tasks()->where('estatus', 'CERRADA')->count();
        $progress = (int) round(($closed / $total) * 100);

        $this->progress = max(0, min(100, $progress));

        // opcional: estatus automático según porcentaje
        $this->estatus = $this->progress === 100 ? 'CERRADO' : ($this->progress > 0 ? 'EN_PROCESO' : 'ABIERTO');

        $this->save();
    }
}
