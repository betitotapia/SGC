<?php

namespace App\Support;

use App\Models\AuditLog;

class Audit
{
    public static function deleted(object $entity, array $before = []): void
    {
        $user = request()->user();

        AuditLog::create([
            'user_id' => $user?->id,
            'action' => 'deleted',
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id ?? null,
            'before' => $before ?: (method_exists($entity, 'toArray') ? $entity->toArray() : []),
            'meta' => [
                'ip' => request()->ip(),
                'ua' => request()->userAgent(),
                'url' => request()->fullUrl(),
            ],
        ]);
    }
}
