<?php

namespace App\Services\Csf;

use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Extrae los datos fiscales de la Constancia de Situación Fiscal (SAT).
 *
 * Se apoya en smalot/pdfparser. El formato del PDF del SAT no está
 * oficialmente documentado, así que usamos una combinación de etiquetas
 * conocidas + expresiones regulares sobre el texto plano.
 */
class CsfParser
{
    public function __construct(private ?PdfParser $parser = null)
    {
        $this->parser ??= new PdfParser();
    }

    public function parseFile(string $path): CsfData
    {
        if (! is_readable($path)) {
            throw new RuntimeException("No se puede leer el archivo CSF: {$path}");
        }

        $text = $this->parser->parseFile($path)->getText();

        return $this->parseText($text);
    }

    public function parseText(string $text): CsfData
    {
        // Normalización de espacios y saltos
        $normalized = preg_replace("/\r\n|\r/", "\n", $text);
        $normalized = preg_replace('/[ \t]+/', ' ', $normalized);
        $normalized = preg_replace('/\n{2,}/', "\n", $normalized);

        $data = new CsfData();
        $data->raw = ['text' => $normalized];

        $data->rfc              = $this->match($normalized, '/\bRFC:?\s*([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})\b/i');
        $data->legalName        = $this->labelValue($normalized, ['Denominación/Razón Social','Denominación o Razón Social','Nombre, denominación o razón social','Nombre \(s\)'], 180);
        $data->commercialName   = $this->labelValue($normalized, ['Nombre Comercial','Nombre comercial'], 120);
        $data->taxRegimeCode    = $this->match($normalized, '/R[eé]gimen(?:es)?\s*(?:Fiscal(?:es)?)?[^\n]*?(\d{3})\b/i');
        $data->taxRegimeName    = $this->labelValue($normalized, ['Régimen','Regimen Fiscal','Régimen Fiscal'], 120);
        $data->zip              = $this->match($normalized, '/C[oó]digo\s+Postal:?\s*(\d{5})/i') ?? $this->match($normalized, '/\bC\.?P\.?\s*:?\s*(\d{5})\b/i');
        $data->street           = $this->labelValue($normalized, ['Nombre de Vialidad','Nombre de la Vialidad','Calle'], 120);
        $data->exteriorNumber   = $this->labelValue($normalized, ['Número Exterior','Numero Exterior','No\. Exterior'], 20);
        $data->interiorNumber   = $this->labelValue($normalized, ['Número Interior','Numero Interior','No\. Interior'], 20);
        $data->neighborhood     = $this->labelValue($normalized, ['Nombre de la Colonia','Colonia'], 120);
        $data->municipality     = $this->labelValue($normalized, ['Nombre del Municipio o Demarcación Territorial','Municipio','Delegación','Demarcación Territorial'], 120);
        $data->state            = $this->labelValue($normalized, ['Nombre de la Entidad Federativa','Entidad Federativa','Estado'], 120);
        $data->email            = $this->match($normalized, '/([\w\.\-\+]+@[\w\-]+\.[\w\-\.]+)/');

        if ($data->rfc) {
            $data->rfc = strtoupper(trim($data->rfc));
        }

        return $data;
    }

    // ---- helpers ----

    private function match(string $text, string $pattern): ?string
    {
        if (preg_match($pattern, $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Busca "Etiqueta: valor" permitiendo que el valor esté en la misma línea
     * o en la siguiente. Se prueba una lista de posibles etiquetas.
     */
    private function labelValue(string $text, array $labels, int $maxLen = 120): ?string
    {
        foreach ($labels as $label) {
            $pattern = '/' . $label . '\s*[:\-]?\s*([^\n]{1,' . $maxLen . '})/i';
            if (preg_match($pattern, $text, $m)) {
                $value = trim($m[1]);
                // Evita capturar otra etiqueta (dos puntos al inicio del siguiente campo)
                $value = preg_split('/\s{2,}|  |\t/', $value)[0] ?? $value;
                if ($value !== '' && !preg_match('/^[:\-]/', $value)) {
                    return $value;
                }
            }
        }
        return null;
    }
}
