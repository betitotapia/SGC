<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $u): bool { return $u->can('warehouses.view'); }
    public function view(User $u, Warehouse $w): bool { return $u->can('warehouses.view'); }
    public function create(User $u): bool { return $u->can('warehouses.create'); }
    public function update(User $u, ?Warehouse $w = null): bool { return $u->can('warehouses.update'); }
    public function delete(User $u, Warehouse $w): bool { return $u->can('warehouses.delete'); }
}
