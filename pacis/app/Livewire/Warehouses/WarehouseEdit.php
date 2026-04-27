<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Almacén')]
class WarehouseEdit extends Component
{
    public ?Warehouse $warehouse = null;

    public string $code = '';
    public string $name = '';
    public ?string $address = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $zip = null;
    public ?string $phone = null;
    public ?string $manager = null;
    public ?string $notes = null;
    public bool $active = true;
    public bool $is_default = false;

    public function mount(?Warehouse $warehouse = null): void
    {
        if ($warehouse?->exists) {
            $this->warehouse = $warehouse;
            $this->fill($warehouse->only([
                'code','name','address','city','state','zip','phone','manager','notes','active','is_default',
            ]));
        }
    }

    protected function rules(): array
    {
        return [
            'code' => ['required','string','max:20',
                Rule::unique('warehouses','code')->ignore($this->warehouse?->id),
            ],
            'name' => ['required','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'city'    => ['nullable','string','max:120'],
            'state'   => ['nullable','string','max:120'],
            'zip'     => ['nullable','string','max:10'],
            'phone'   => ['nullable','string','max:20'],
            'manager' => ['nullable','string','max:120'],
            'notes'   => ['nullable','string','max:1000'],
            'active'  => ['boolean'],
            'is_default' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->authorize($this->warehouse ? 'update' : 'create', $this->warehouse ?? Warehouse::class);
        $data = $this->validate();

        if ($this->warehouse) {
            $this->warehouse->update($data);
        } else {
            $this->warehouse = Warehouse::create($data);
        }

        if ($this->is_default) {
            Warehouse::where('id','!=',$this->warehouse->id)->update(['is_default' => false]);
        }

        session()->flash('success', 'Almacén guardado correctamente.');
        return redirect()->route('warehouses.index');
    }

    public function render()
    {
        return view('livewire.warehouses.edit');
    }
}
