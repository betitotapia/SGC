<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\FiscalProfile;
use App\Models\User;
use App\Services\Csf\CsfParser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Cliente')]
class CustomerEdit extends Component
{
    use WithFileUploads;

    public ?Customer $customer = null;

    // Customer
    public string $code = '';
    public string $display_name = '';
    public ?string $contact_name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public float $credit_limit = 0;
    public int $credit_days = 0;
    public ?string $price_list = null;
    public ?string $notes = null;
    public ?int $seller_id = null;
    public bool $active = true;

    // Fiscal profile
    public ?string $rfc = null;
    public ?string $legal_name = null;
    public ?string $commercial_name = null;
    public ?string $tax_regime_code = null;
    public ?string $tax_regime_name = null;
    public ?string $cfdi_use = 'G03';
    public ?string $zip = null;
    public ?string $street = null;
    public ?string $exterior_number = null;
    public ?string $interior_number = null;
    public ?string $neighborhood = null;
    public ?string $municipality = null;
    public ?string $state = null;
    public ?string $fiscal_email = null;

    // CSF
    public $csf_file;
    public ?string $existing_csf_path = null;

    public function mount(?Customer $customer = null): void
    {
        if ($customer?->exists) {
            $this->customer = $customer->load('fiscalProfile');
            $this->fill($customer->only([
                'code','display_name','contact_name','email','phone',
                'credit_limit','credit_days','price_list','notes','seller_id','active',
            ]));
            if ($customer->fiscalProfile) {
                $fp = $customer->fiscalProfile;
                $this->rfc = $fp->rfc;
                $this->legal_name = $fp->legal_name;
                $this->commercial_name = $fp->commercial_name;
                $this->tax_regime_code = $fp->tax_regime_code;
                $this->tax_regime_name = $fp->tax_regime_name;
                $this->cfdi_use = $fp->cfdi_use;
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
                Rule::unique('customers','code')->ignore($this->customer?->id),
            ],
            'display_name' => ['required','string','max:255'],
            'contact_name' => ['nullable','string','max:255'],
            'email'        => ['nullable','email','max:255'],
            'phone'        => ['nullable','string','max:20'],
            'credit_limit' => ['numeric','min:0'],
            'credit_days'  => ['integer','min:0'],
            'price_list'   => ['nullable','string','max:80'],
            'notes'        => ['nullable','string','max:2000'],
            'seller_id'    => ['nullable','exists:users,id'],
            'active'       => ['boolean'],

            'rfc' => ['nullable','string','size:12|size:13',
                Rule::unique('fiscal_profiles','rfc')->ignore($this->customer?->fiscal_profile_id),
            ],
            'legal_name' => ['nullable','string','max:255'],
            'commercial_name' => ['nullable','string','max:255'],
            'tax_regime_code' => ['nullable','string','max:10'],
            'tax_regime_name' => ['nullable','string','max:150'],
            'cfdi_use' => ['nullable','string','max:10'],
            'zip' => ['nullable','string','max:10'],
            'street' => ['nullable','string','max:255'],
            'exterior_number' => ['nullable','string','max:20'],
            'interior_number' => ['nullable','string','max:20'],
            'neighborhood' => ['nullable','string','max:150'],
            'municipality' => ['nullable','string','max:150'],
            'state' => ['nullable','string','max:150'],
            'fiscal_email' => ['nullable','email','max:255'],
            'csf_file'     => ['nullable','file','mimes:pdf','max:6144'],
        ];
    }

    /**
     * Sube el PDF de CSF y autorellenado los campos fiscales con CsfParser.
     */
    public function parseCsf(CsfParser $parser): void
    {
        $this->validate(['csf_file' => ['required','file','mimes:pdf','max:6144']]);
        $path = $this->csf_file->getRealPath();
        $data = $parser->parseFile($path);

        if (! $data->isValid()) {
            $this->addError('csf_file', 'No se pudieron extraer datos básicos (RFC o Razón social) de la CSF.');
            return;
        }

        $this->rfc              = $data->rfc ?? $this->rfc;
        $this->legal_name       = $data->legalName ?? $this->legal_name;
        $this->commercial_name  = $data->commercialName ?? $this->commercial_name;
        $this->tax_regime_code  = $data->taxRegimeCode ?? $this->tax_regime_code;
        $this->tax_regime_name  = $data->taxRegimeName ?? $this->tax_regime_name;
        $this->zip              = $data->zip ?? $this->zip;
        $this->street           = $data->street ?? $this->street;
        $this->exterior_number  = $data->exteriorNumber ?? $this->exterior_number;
        $this->interior_number  = $data->interiorNumber ?? $this->interior_number;
        $this->neighborhood     = $data->neighborhood ?? $this->neighborhood;
        $this->municipality     = $data->municipality ?? $this->municipality;
        $this->state            = $data->state ?? $this->state;
        $this->fiscal_email     = $data->email ?? $this->fiscal_email;

        session()->flash('success', 'Datos extraídos de la CSF. Revisa y guarda.');
    }

    public function save()
    {
        $this->authorize($this->customer ? 'update' : 'create', $this->customer ?? Customer::class);
        $data = $this->validate();

        // Fiscal profile
        $fiscalId = $this->customer?->fiscal_profile_id;
        if ($this->rfc && $this->legal_name) {
            $fpData = [
                'rfc'             => strtoupper(trim($this->rfc)),
                'legal_name'      => $this->legal_name,
                'commercial_name' => $this->commercial_name,
                'tax_regime_code' => $this->tax_regime_code,
                'tax_regime_name' => $this->tax_regime_name,
                'cfdi_use'        => $this->cfdi_use,
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
                $fpData['csf_file_path'] = $this->csf_file->store('customers', 'csf');
            }

            $fp = $fiscalId
                ? tap(FiscalProfile::findOrFail($fiscalId))->update($fpData)
                : FiscalProfile::create($fpData);
            $fiscalId = $fp->id;
        }

        $payload = array_merge(collect($data)->only([
            'code','display_name','contact_name','email','phone',
            'credit_limit','credit_days','price_list','notes','seller_id','active',
        ])->all(), ['fiscal_profile_id' => $fiscalId]);

        if ($this->customer) {
            $this->customer->update($payload);
        } else {
            $this->customer = Customer::create($payload);
        }

        session()->flash('success', 'Cliente guardado.');
        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.edit', [
            'sellers' => User::active()->role(config('pacis.roles.vendedor'))->orderBy('name')->get(),
        ]);
    }
}
