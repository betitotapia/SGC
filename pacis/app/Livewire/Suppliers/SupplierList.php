<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Proveedores')]
class SupplierList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $suppliers = Supplier::query()
            ->with('fiscalProfile')
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('display_name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhereHas('fiscalProfile', fn ($fp) => $fp->where('rfc', 'like', "%{$this->search}%"));
            }))
            ->orderBy('display_name')
            ->paginate(20);

        return view('livewire.suppliers.list', compact('suppliers'));
    }
}
