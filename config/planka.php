<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Planka Application URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL where your Planka instance is accessible.
    | This will be used for generating links to cards, boards, etc.
    |
    */
    'url' => env('PLANKA_URL', 'http://localhost:1337'),
    
    /*
    |--------------------------------------------------------------------------
    | Planka Database Connection
    |--------------------------------------------------------------------------
    |
    | These values are used to configure the database connection to your
    | Planka instance. The companion app reads data directly from the
    | Planka database.
    |
    */
    'database' => [
        'host' => env('PLANKA_DB_HOST', '127.0.0.1'),
        'port' => env('PLANKA_DB_PORT', '5432'),
        'database' => env('PLANKA_DB_DATABASE', 'planka'),
        'username' => env('PLANKA_DB_USERNAME', 'planka'),
        'password' => env('PLANKA_DB_PASSWORD', ''),
    ],
];