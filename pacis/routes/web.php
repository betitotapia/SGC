<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Customers\CustomerEdit;
use App\Livewire\Customers\CustomerList;
use App\Livewire\Dashboard\DashboardPage;
use App\Livewire\Products\ProductEdit;
use App\Livewire\Products\ProductList;
use App\Livewire\Suppliers\SupplierEdit;
use App\Livewire\Suppliers\SupplierList;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserList;
use App\Livewire\Warehouses\WarehouseEdit;
use App\Livewire\Warehouses\WarehouseList;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');

    // Perfil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Almacenes
    Route::middleware('permission:warehouses.view')->group(function () {
        Route::get('/warehouses',              WarehouseList::class)->name('warehouses.index');
        Route::get('/warehouses/create',       WarehouseEdit::class)->name('warehouses.create');
        Route::get('/warehouses/{warehouse}/edit', WarehouseEdit::class)->name('warehouses.edit');
    });

    // Productos
    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products',               ProductList::class)->name('products.index');
        Route::get('/products/create',        ProductEdit::class)->name('products.create');
        Route::get('/products/{product}/edit',ProductEdit::class)->name('products.edit');
    });

    // Clientes
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('/customers',               CustomerList::class)->name('customers.index');
        Route::get('/customers/create',        CustomerEdit::class)->name('customers.create');
        Route::get('/customers/{customer}/edit',CustomerEdit::class)->name('customers.edit');
    });

    // Proveedores
    Route::middleware('permission:suppliers.view')->group(function () {
        Route::get('/suppliers',               SupplierList::class)->name('suppliers.index');
        Route::get('/suppliers/create',        SupplierEdit::class)->name('suppliers.create');
        Route::get('/suppliers/{supplier}/edit',SupplierEdit::class)->name('suppliers.edit');
    });

    // Usuarios (admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',           UserList::class)->name('users.index');
        Route::get('/users/create',    UserEdit::class)->name('users.create');
        Route::get('/users/{user}/edit',UserEdit::class)->name('users.edit');
    });
});

require __DIR__.'/auth.php';
