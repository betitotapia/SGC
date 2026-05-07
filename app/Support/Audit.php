<?php

namespace App\Support;

use App\Models\AuditLog;

class Audit
{
    public static function deleted(object $entity, array $before = []): void
    {
        static::log('deleted', $entity, $before ?: (method_exists($entity, 'toArray') ? $entity->toArray() : []));
    }

    public static function log(string $action, object $entity, array $before = null, array $after = null): void
    {
        $user = request()->user();

        AuditLog::create([
            'user_id'     => $user?->id,
            'action'      => $action,
            'entity_type' => get_class($entity),
            'entity_id'   => $entity->id ?? null,
            'before'      => $before,
            'meta'        => [
                'after' => $after,
                'ip'    => request()->ip(),
                'ua'    => request()->userAgent(),
                'url'   => request()->fullUrl(),
            ],
        ]);
    }
}
