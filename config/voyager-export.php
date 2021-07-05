<?php

return [

    /*
     * The config_key for voyager-export package.
     */
    'config_key' => env('VOYAGER_EXPORT_CONFIG_KEY', 'joy-voyager-export'),

    /*
     * The route_prefix for voyager-export package.
     */
    'route_prefix' => env('VOYAGER_EXPORT_ROUTE_PREFIX', 'joy-voyager-export'),

    /*
    |--------------------------------------------------------------------------
    | Controllers config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager controller settings
    |
    */

    'controllers' => [
        'namespace' => 'Joy\\VoyagerExport\\Http\\Controllers',
    ],
];
