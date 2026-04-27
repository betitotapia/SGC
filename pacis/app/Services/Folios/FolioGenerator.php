<?php

namespace App\Services\Folios;

use Illuminate\Support\Facades\DB;

/**
 * Generador simple de folios consecutivos con prefijo y cero-padding.
 *
 *   PO-000001, REM-000001, REC-000001
 *
 * Para producción se recomienda sustituir por un mecanismo con bloqueo
 * de fila o tabla de contadores si se tiene alta concurrencia.
 */
class FolioGenerator
{
    public function next(string $prefix, string $table, string $column = 'folio', int $pad = 6): string
    {
        return DB::transaction(function () use ($prefix, $table, $column, $pad) {
            $last = DB::table($table)
                ->where($column, 'like', $prefix . '-%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value($column);

            $number = 1;
            if ($last && preg_match('/(\d+)$/', $last, $m)) {
                $number = ((int) $m[1]) + 1;
            }

            return sprintf('%s-%s', $prefix, str_pad((string) $number, $pad, '0', STR_PAD_LEFT));
        });
    }
}
