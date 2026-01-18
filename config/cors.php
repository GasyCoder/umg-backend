<?php 

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => (function () {
        $defaults = [
            'http://localhost:3000',
            'https://mahajanga-univ.mg',
            'https://www.mahajanga-univ.mg',
            'https://back.mahajanga-univ.mg',
            'https://api.mahajanga-univ.mg',
        ];

        $raw = (string) env('FRONTEND_URLS', '');

        // Accept comma-separated OR whitespace-separated lists (common misconfig on prod env vars).
        $fromEnv = $raw !== ''
            ? preg_split('/\s*,\s*|\s+/u', $raw, -1, PREG_SPLIT_NO_EMPTY)
            : [];

        $origins = array_merge($defaults, is_array($fromEnv) ? $fromEnv : []);
        $origins = array_values(array_unique(array_map('trim', $origins)));
        $origins = array_values(array_filter($origins, fn($o) => $o !== ''));

        return $origins;
    })(),
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition'],
    'allowed_origins_patterns' => [
        '#^https://([a-z0-9-]+\\.)?mahajanga-univ\\.mg$#',
    ],
    'max_age' => 0,
    'supports_credentials' => true,
];
