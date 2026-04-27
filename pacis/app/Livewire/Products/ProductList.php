<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Productos')]
class ProductList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $products = Product::query()
            ->with(['category','unit'])
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->orderBy('description')
            ->paginate(20);

        return view('livewire.products.list', compact('products'));
    }
}
