<?php 

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_map('trim', explode(',', env('FRONTEND_URLS', implode(',', [
        'http://localhost:3000',
        'https://mahajanga-univ.mg',
        'https://www.mahajanga-univ.mg',
        'https://back.mahajanga-univ.mg',
        'https://api.mahajanga-univ.mg',
    ])))),
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition'],
    'max_age' => 0,
    'supports_credentials' => true,
];
