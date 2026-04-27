<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $u): bool { return $u->can('customers.view'); }
    public function view(User $u, Customer $c): bool { return $u->can('customers.view'); }
    public function create(User $u): bool { return $u->can('customers.create'); }
    public function update(User $u, ?Customer $c = null): bool { return $u->can('customers.update'); }
    public function delete(User $u, Customer $c): bool { return $u->can('customers.delete'); }
}
