<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'serve'  => true,
            'throw'  => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        'csf' => [
            'driver'     => 'local',
            'root'       => storage_path('app/csf'),
            'visibility' => 'private',
            'throw'      => false,
        ],

        'barcodes' => [
            'driver'     => 'local',
            'root'       => storage_path('app/barcodes'),
            'url'        => env('APP_URL').'/barcodes',
            'visibility' => 'public',
            'throw'      => false,
        ],
    ],

    'links' => [
        public_path('storage')  => storage_path('app/public'),
        public_path('barcodes') => storage_path('app/barcodes'),
    ],
];
