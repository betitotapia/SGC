<?php

return [
    /*
    |----------------------------------------------------------
    | Barcode
    |----------------------------------------------------------
    | Prefijo EAN/UPC-A interno (rango 200-299 reservado "in-store")
    | y formato por defecto para generar códigos nuevos.
    */
    'barcode' => [
        'prefix' => env('PACIS_BARCODE_PREFIX', '200'),
        'format' => env('PACIS_BARCODE_FORMAT', 'C128'),
    ],

    /*
    |----------------------------------------------------------
    | Inventario
    |----------------------------------------------------------
    | FEFO (First-Expire-First-Out) sugerido automáticamente al
    | momento de remisionar. Si se desactiva, se seleccionará
    | manualmente el lote.
    */
    'inventory' => [
        'fefo'             => env('PACIS_FEFO_ENABLED', true),
        'alert_days_before_expiry' => 60,
        'allow_negative_stock'     => false,
    ],

    /*
    |----------------------------------------------------------
    | Roles
    |----------------------------------------------------------
    */
    'roles' => [
        'admin'       => 'admin',
        'vendedor'    => 'vendedor',
        'facturacion' => 'facturacion',
        'almacen'     => 'almacen',
    ],

    /*
    |----------------------------------------------------------
    | Facturama (fase 3)
    |----------------------------------------------------------
    */
    'facturama' => [
        'base_url' => env('FACTURAMA_BASE_URL', 'https://api.facturama.mx/'),
        'user'     => env('FACTURAMA_USER'),
        'password' => env('FACTURAMA_PASSWORD'),
        'sandbox'  => env('FACTURAMA_SANDBOX', true),
    ],
];
