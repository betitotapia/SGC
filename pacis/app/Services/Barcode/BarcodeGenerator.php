<?php

namespace App\Services\Barcode;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * Generador de códigos de barras para productos.
 *
 * - Si el producto ya trae un código (por ejemplo EAN-13 del fabricante),
 *   solo lo guarda tal cual.
 * - Si no trae, genera un Code 128 tomando la referencia como base, con
 *   prefijo interno configurable para evitar colisiones con EAN-13 reales.
 */
class BarcodeGenerator
{
    public function __construct(
        private BarcodeGeneratorPNG $png = new BarcodeGeneratorPNG(),
        private BarcodeGeneratorSVG $svg = new BarcodeGeneratorSVG(),
    ) {
    }

    /**
     * Genera un código único basado en la referencia del producto.
     * Se garantiza unicidad por tabla products.barcode.
     */
    public function generateCode(string $reference): string
    {
        $prefix = (string) config('pacis.barcode.prefix', '200');
        $clean  = Str::of($reference)
            ->upper()
            ->replace(' ', '')
            ->replaceMatches('/[^A-Z0-9\-]/', '')
            ->substr(0, 18)
            ->value();

        return $prefix . '-' . $clean;
    }

    /**
     * Retorna PNG binario de un código de barras usando formato definido en config.
     */
    public function renderPng(string $code, int $widthFactor = 2, int $height = 60): string
    {
        return $this->png->getBarcode($code, $this->format(), $widthFactor, $height);
    }

    public function renderSvg(string $code, int $widthFactor = 2, int $height = 60): string
    {
        return $this->svg->getBarcode($code, $this->format(), $widthFactor, $height);
    }

    /**
     * Guarda el PNG del código y devuelve la ruta relativa al disco 'barcodes'.
     */
    public function storePng(string $code): string
    {
        $filename = 'products/' . md5($code) . '.png';
        Storage::disk('barcodes')->put($filename, $this->renderPng($code));
        return $filename;
    }

    private function format(): string
    {
        return match (strtoupper((string) config('pacis.barcode.format', 'C128'))) {
            'EAN13' => BarcodeGeneratorPNG::TYPE_EAN_13,
            'CODE39','C39' => BarcodeGeneratorPNG::TYPE_CODE_39,
            default => BarcodeGeneratorPNG::TYPE_CODE_128,
        };
    }
}
