<?php

namespace App\Services\Csf;

/**
 * DTO con los datos parseados de la Constancia de Situación Fiscal (CSF).
 */
final class CsfData
{
    public function __construct(
        public ?string $rfc = null,
        public ?string $legalName = null,
        public ?string $commercialName = null,
        public ?string $taxRegimeCode = null,
        public ?string $taxRegimeName = null,
        public ?string $zip = null,
        public ?string $street = null,
        public ?string $exteriorNumber = null,
        public ?string $interiorNumber = null,
        public ?string $neighborhood = null,
        public ?string $municipality = null,
        public ?string $state = null,
        public ?string $email = null,
        public array $raw = [],
    ) {
    }

    public function isValid(): bool
    {
        return (bool) $this->rfc && (bool) $this->legalName;
    }

    public function toArray(): array
    {
        return [
            'rfc'              => $this->rfc,
            'legal_name'       => $this->legalName,
            'commercial_name'  => $this->commercialName,
            'tax_regime_code'  => $this->taxRegimeCode,
            'tax_regime_name'  => $this->taxRegimeName,
            'zip'              => $this->zip,
            'street'           => $this->street,
            'exterior_number'  => $this->exteriorNumber,
            'interior_number'  => $this->interiorNumber,
            'neighborhood'     => $this->neighborhood,
            'municipality'     => $this->municipality,
            'state'            => $this->state,
            'email'            => $this->email,
            'csf_raw'          => $this->raw,
        ];
    }
}
