<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Services\Barcode\BarcodeGenerator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Producto')]
class ProductEdit extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $reference = '';
    public ?string $alt_key = null;
    public ?string $barcode = null;
    public bool $barcode_generated = false;
    public string $description = '';
    public ?string $long_description = null;
    public ?string $brand = null;
    public ?string $presentation = null;
    public ?int $category_id = null;
    public ?int $unit_id = null;
    public bool $requires_lot = true;
    public bool $requires_expiry = true;
    public bool $controlled = false;
    public ?string $sat_product_code = null;
    public float $cost = 0;
    public float $price = 0;
    public float $tax_rate = 0.16;
    public int $min_stock = 0;
    public ?int $max_stock = null;
    public bool $active = true;
    public $image;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->fill($product->only([
                'reference','alt_key','barcode','barcode_generated',
                'description','long_description','brand','presentation',
                'category_id','unit_id',
                'requires_lot','requires_expiry','controlled','sat_product_code',
                'cost','price','tax_rate','min_stock','max_stock','active',
            ]));
        }
    }

    protected function rules(): array
    {
        return [
            'reference' => ['required','string','max:60',
                Rule::unique('products','reference')->ignore($this->product?->id),
            ],
            'alt_key'   => ['nullable','string','max:60'],
            'barcode'   => ['nullable','string','max:64',
                Rule::unique('products','barcode')->ignore($this->product?->id),
            ],
            'description' => ['required','string','max:255'],
            'long_description' => ['nullable','string','max:5000'],
            'brand'        => ['nullable','string','max:120'],
            'presentation' => ['nullable','string','max:120'],
            'category_id'  => ['nullable','exists:product_categories,id'],
            'unit_id'      => ['nullable','exists:product_units,id'],
            'requires_lot' => ['boolean'],
            'requires_expiry' => ['boolean'],
            'controlled'   => ['boolean'],
            'sat_product_code' => ['nullable','string','max:10'],
            'cost'         => ['required','numeric','min:0'],
            'price'        => ['required','numeric','min:0'],
            'tax_rate'     => ['required','numeric','min:0','max:1'],
            'min_stock'    => ['required','integer','min:0'],
            'max_stock'    => ['nullable','integer','min:0'],
            'active'       => ['boolean'],
            'image'        => ['nullable','image','max:4096'],
        ];
    }

    public function generateBarcode(BarcodeGenerator $gen): void
    {
        if (! $this->reference) {
            $this->addError('reference', 'Captura primero la referencia.');
            return;
        }
        $this->barcode = $gen->generateCode($this->reference);
        $this->barcode_generated = true;
    }

    public function save(BarcodeGenerator $gen)
    {
        $this->authorize($this->product ? 'update' : 'create', $this->product ?? Product::class);
        $data = $this->validate();

        // Si no trae código y el usuario no lo generó manualmente, lo generamos automático.
        if (empty($data['barcode'])) {
            $data['barcode'] = $gen->generateCode($data['reference']);
            $data['barcode_generated'] = true;
        }

        if ($this->image) {
            $data['image_path'] = $this->image->store('products', 'public');
        }
        unset($data['image']);

        if ($this->product) {
            $this->product->update($data);
        } else {
            $this->product = Product::create($data);
        }

        session()->flash('success', 'Producto guardado.');
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.edit', [
            'categories' => ProductCategory::orderBy('name')->get(),
            'units'      => ProductUnit::orderBy('name')->get(),
        ]);
    }
}
