<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('PACIS_DEFAULT_ADMIN_EMAIL', 'admin@pacis.local');
        $pass  = env('PACIS_DEFAULT_ADMIN_PASSWORD', 'PacisAdmin#2026');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Administrador PACIS',
                'password'          => Hash::make($pass),
                'email_verified_at' => now(),
                'active'            => true,
            ],
        );

        if (! $admin->hasRole(config('pacis.roles.admin'))) {
            $admin->assignRole(config('pacis.roles.admin'));
        }

        // Usuarios demo (solo en non-production)
        if (app()->environment('local', 'testing')) {
            $demo = [
                ['name' => 'Vendedor Demo',   'email' => 'vendedor@pacis.local',   'role' => config('pacis.roles.vendedor')],
                ['name' => 'Facturación Demo','email' => 'facturacion@pacis.local','role' => config('pacis.roles.facturacion')],
                ['name' => 'Almacén Demo',    'email' => 'almacen@pacis.local',    'role' => config('pacis.roles.almacen')],
            ];
            foreach ($demo as $u) {
                $user = User::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'name'              => $u['name'],
                        'password'          => Hash::make('password'),
                        'email_verified_at' => now(),
                        'active'            => true,
                    ],
                );
                if (! $user->hasRole($u['role'])) {
                    $user->assignRole($u['role']);
                }
            }
        }
    }
}
