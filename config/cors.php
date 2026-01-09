<?php 

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:3000'))),
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition'],
    'supports_credentials' => false,
];
