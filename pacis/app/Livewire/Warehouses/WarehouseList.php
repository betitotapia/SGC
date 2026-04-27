<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Almacenes')]
class WarehouseList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'all'; // all|active|inactive

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function toggle(Warehouse $warehouse): void
    {
        $this->authorize('update', $warehouse);
        $warehouse->update(['active' => ! $warehouse->active]);
        session()->flash('success', 'Estatus actualizado.');
    }

    public function render()
    {
        $warehouses = Warehouse::query()
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'active',   fn ($q) => $q->where('active', true))
            ->when($this->status === 'inactive', fn ($q) => $q->where('active', false))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.warehouses.list', compact('warehouses'));
    }
}
