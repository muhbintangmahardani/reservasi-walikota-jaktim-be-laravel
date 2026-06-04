<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Izinkan semua rute API dan rute web
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    // Izinkan semua metode (GET, POST, PUT, DELETE, OPTIONS)
    'allowed_methods' => ['*'],

    // 🔥 TULIS SPESIFIK DOMAIN VERCEL & LOCALHOST (JANGAN PAKAI BINTANG *) 🔥
    'allowed_origins' => [
        'https://reservasikominfotikjaktim.vercel.app',
        'http://localhost:3000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 🔥 INI WAJIB TRUE KARENA AXIOS ANDA MENGIRIMKAN COOKIE/TOKEN 🔥
    'supports_credentials' => true,

];