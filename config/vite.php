<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Vite Plugin Configuration
    |--------------------------------------------------------------------------
    */

    'dev_server' => [
        'enabled' => false, // 🔒 Force Laravel to use built files only
        'url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),
    ],

    'build_path' => 'build',

];
