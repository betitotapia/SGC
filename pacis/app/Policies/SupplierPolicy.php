<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $u): bool { return $u->can('suppliers.view'); }
    public function view(User $u, Supplier $s): bool { return $u->can('suppliers.view'); }
    public function create(User $u): bool { return $u->can('suppliers.create'); }
    public function update(User $u, ?Supplier $s = null): bool { return $u->can('suppliers.update'); }
    public function delete(User $u, Supplier $s): bool { return $u->can('suppliers.delete'); }
}
