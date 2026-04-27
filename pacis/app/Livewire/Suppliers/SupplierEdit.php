<?php

namespace App\Livewire\Suppliers;

use App\Models\FiscalProfile;
use App\Models\Supplier;
use App\Services\Csf\CsfParser;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Proveedor')]
class SupplierEdit extends Component
{
    use WithFileUploads;

    public ?Supplier $supplier = null;

    public string $code = '';
    public string $display_name = '';
    public ?string $contact_name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public int $lead_time_days = 0;
    public ?string $notes = null;
    public bool $active = true;

    public ?string $rfc = null;
    public ?string $legal_name = null;
    public ?string $commercial_name = null;
    public ?string $tax_regime_code = null;
    public ?string $tax_regime_name = null;
    public ?string $zip = null;
    public ?string $street = null;
    public ?string $exterior_number = null;
    public ?string $interior_number = null;
    public ?string $neighborhood = null;
    public ?string $municipality = null;
    public ?string $state = null;
    public ?string $fiscal_email = null;

    public $csf_file;
    public ?string $existing_csf_path = null;

    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier?->exists) {
            $this->supplier = $supplier->load('fiscalProfile');
            $this->fill($supplier->only([
                'code','display_name','contact_name','email','phone',
                'lead_time_days','notes','active',
            ]));
            if ($supplier->fiscalProfile) {
                $fp = $supplier->fiscalProfile;
                $this->rfc = $fp->rfc;
                $this->legal_name = $fp->legal_name;
                $this->commercial_name = $fp->commercial_name;
                $this->tax_regime_code = $fp->tax_regime_code;
                $this->tax_regime_name = $fp->tax_regime_name;
                $this->zip = $fp->zip;
                $this->street = $fp->street;
                $this->exterior_number = $fp->exterior_number;
                $this->interior_number = $fp->interior_number;
                $this->neighborhood = $fp->neighborhood;
                $this->municipality = $fp->municipality;
                $this->state = $fp->state;
                $this->fiscal_email = $fp->email;
                $this->existing_csf_path = $fp->csf_file_path;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'code' => ['required','string','max:30',
                Rule::unique('suppliers','code')->ignore($this->supplier?->id),
            ],
            'display_name' => ['required','string','max:255'],
            'contact_name' => ['nullable','string','max:255'],
            'email'        => ['nullable','email','max:255'],
            'phone'        => ['nullable','string','max:20'],
            'lead_time_days' => ['integer','min:0'],
            'notes'        => ['nullable','string','max:2000'],
            'active'       => ['boolean'],

            'rfc' => ['nullable','string','size:12|size:13',
                Rule::unique('fiscal_profiles','rfc')->ignore($this->supplier?->fiscal_profile_id),
            ],
            'legal_name' => ['nullable','string','max:255'],
            'commercial_name' => ['nullable','string','max:255'],
            'tax_regime_code' => ['nullable','string','max:10'],
            'tax_regime_name' => ['nullable','string','max:150'],
            'zip' => ['nullable','string','max:10'],
            'street' => ['nullable','string','max:255'],
            'exterior_number' => ['nullable','string','max:20'],
            'interior_number' => ['nullable','string','max:20'],
            'neighborhood' => ['nullable','string','max:150'],
            'municipality' => ['nullable','string','max:150'],
            'state' => ['nullable','string','max:150'],
            'fiscal_email' => ['nullable','email','max:255'],
            'csf_file' => ['nullable','file','mimes:pdf','max:6144'],
        ];
    }

    public function parseCsf(CsfParser $parser): void
    {
        $this->validate(['csf_file' => ['required','file','mimes:pdf','max:6144']]);
        $data = $parser->parseFile($this->csf_file->getRealPath());
        if (! $data->isValid()) {
            $this->addError('csf_file', 'No se pudieron extraer datos básicos de la CSF.');
            return;
        }
        foreach ($data->toArray() as $key => $value) {
            $map = [
                'rfc' => 'rfc',
                'legal_name' => 'legal_name',
                'commercial_name' => 'commercial_name',
                'tax_regime_code' => 'tax_regime_code',
                'tax_regime_name' => 'tax_regime_name',
                'zip' => 'zip',
                'street' => 'street',
                'exterior_number' => 'exterior_number',
                'interior_number' => 'interior_number',
                'neighborhood' => 'neighborhood',
                'municipality' => 'municipality',
                'state' => 'state',
                'email' => 'fiscal_email',
            ];
            if (isset($map[$key]) && $value !== null) {
                $this->{$map[$key]} = $value;
            }
        }
        session()->flash('success', 'Datos extraídos de la CSF.');
    }

    public function save()
    {
        $this->authorize($this->supplier ? 'update' : 'create', $this->supplier ?? Supplier::class);
        $data = $this->validate();

        $fiscalId = $this->supplier?->fiscal_profile_id;
        if ($this->rfc && $this->legal_name) {
            $fpData = [
                'rfc'             => strtoupper(trim($this->rfc)),
                'legal_name'      => $this->legal_name,
                'commercial_name' => $this->commercial_name,
                'tax_regime_code' => $this->tax_regime_code,
                'tax_regime_name' => $this->tax_regime_name,
                'zip'             => $this->zip,
                'street'          => $this->street,
                'exterior_number' => $this->exterior_number,
                'interior_number' => $this->interior_number,
                'neighborhood'    => $this->neighborhood,
                'municipality'    => $this->municipality,
                'state'           => $this->state,
                'email'           => $this->fiscal_email,
            ];
            if ($this->csf_file) {
                $fpData['csf_file_path'] = $this->csf_file->store('suppliers', 'csf');
            }
            $fp = $fiscalId
                ? tap(FiscalProfile::findOrFail($fiscalId))->update($fpData)
                : FiscalProfile::create($fpData);
            $fiscalId = $fp->id;
        }

        $payload = array_merge(collect($data)->only([
            'code','display_name','contact_name','email','phone',
            'lead_time_days','notes','active',
        ])->all(), ['fiscal_profile_id' => $fiscalId]);

        if ($this->supplier) {
            $this->supplier->update($payload);
        } else {
            $this->supplier = Supplier::create($payload);
        }

        session()->flash('success', 'Proveedor guardado.');
        return redirect()->route('suppliers.index');
    }

    public function render()
    {
        return view('livewire.suppliers.edit');
    }
}
