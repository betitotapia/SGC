<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Clientes')]
class CustomerList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $customers = Customer::query()
            ->with(['fiscalProfile','seller'])
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('display_name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhereHas('fiscalProfile', fn ($fp) => $fp->where('rfc', 'like', "%{$this->search}%")
                                                              ->orWhere('legal_name', 'like', "%{$this->search}%"));
            }))
            ->orderBy('display_name')
            ->paginate(20);

        return view('livewire.customers.list', compact('customers'));
    }
}
