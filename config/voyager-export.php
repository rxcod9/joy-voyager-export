<?php

return [

    /*
     * If enabled for voyager-export package.
     */
    'enabled' => env('VOYAGER_EXPORT_ENABLED', true),

    /*
    | Here you can specify for which data type slugs export is enabled
    | 
    | Supported: "*", or data type slugs "users", "roles"
    |
    */

    'allowed_slugs' => array_filter(explode(',', env('VOYAGER_EXPORT_ALLOWED_SLUGS', '*'))),

    /*
    | Here you can specify for which data type slugs export is not allowed
    | 
    | Supported: "*", or data type slugs "users", "roles"
    |
    */

    'not_allowed_slugs' => array_filter(explode(',', env('VOYAGER_EXPORT_NOT_ALLOWED_SLUGS', ''))),

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

    /*
    | The default export format for voyager-export package.
    | 
    | Supported: "Xlsx", "Csv", "Csv", "Ods", "Xls",
    |   "Slk", "Xml", "Gnumeric", "Html", "Mpdf", "Dompdf", "Tcpdf"
    |
    */
    'format' => env('VOYAGER_EXPORT_FORMAT', 'Xlsx'),
];
