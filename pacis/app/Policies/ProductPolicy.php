<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $u): bool { return $u->can('products.view'); }
    public function view(User $u, Product $p): bool { return $u->can('products.view'); }
    public function create(User $u): bool { return $u->can('products.create'); }
    public function update(User $u, Product $p): bool { return $u->can('products.update'); }
    public function delete(User $u, Product $p): bool { return $u->can('products.delete'); }
}
