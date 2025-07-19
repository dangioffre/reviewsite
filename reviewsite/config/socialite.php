<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Socialite Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Laravel Socialite package
    |
    */

    'guzzle' => [
        'verify' => env('DISABLE_SSL_VERIFICATION', false) ? false : true,
        'timeout' => 30,
    ],
];