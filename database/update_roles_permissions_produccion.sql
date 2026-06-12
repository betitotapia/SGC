START TRANSACTION;

-- Asegura que existan los permisos usados por las rutas.
INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT 'quality.departments.manage', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `permissions`
    WHERE `name` = 'quality.departments.manage'
      AND `guard_name` = 'web'
);

INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT 'users.manage', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `permissions`
    WHERE `name` = 'users.manage'
      AND `guard_name` = 'web'
);

-- Da acceso a usuarios y departamentos a los cuatro roles solicitados.
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r
WHERE p.`name` IN ('quality.departments.manage', 'users.manage')
  AND p.`guard_name` = 'web'
  AND r.`name` IN ('Admin', 'Gerente de Calidad', 'Coordinador de Calidad', 'Analista de Calidad')
  AND r.`guard_name` = 'web'
  AND NOT EXISTS (
      SELECT 1
      FROM `role_has_permissions` rhp
      WHERE rhp.`permission_id` = p.id
        AND rhp.`role_id` = r.id
  );

-- Limpia caché de permisos de Spatie cuando el cache driver usa base de datos.
DELETE FROM `cache`
WHERE `key` LIKE '%spatie.permission.cache%';

COMMIT;
