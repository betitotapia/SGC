<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Remission;
use App\Models\Stock;
use App\Models\Warehouse;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class DashboardPage extends Component
{
    public function render()
    {
        $alertDays = (int) config('pacis.inventory.alert_days_before_expiry', 60);

        return view('livewire.dashboard.page', [
            'counters' => [
                'products'    => Product::active()->count(),
                'warehouses'  => Warehouse::active()->count(),
                'customers'   => Customer::active()->count(),
                'remissions'  => Remission::whereMonth('issued_at', now()->month)->count(),
            ],
            'expiringSoon' => Stock::with(['product','warehouse','lot'])
                ->whereHas('lot', fn ($q) => $q->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now()->addDays($alertDays)))
                ->where('quantity', '>', 0)
                ->orderBy('warehouse_id')
                ->limit(10)
                ->get(),
            'lowStock' => Product::active()
                ->whereNotNull('min_stock')
                ->where('min_stock', '>', 0)
                ->get()
                ->filter(fn ($p) => $p->availableStock() < $p->min_stock)
                ->take(10),
        ]);
    }
}
