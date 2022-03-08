<?php

declare(strict_types=1);

namespace Joy\VoyagerExport;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Joy\VoyagerExport\Exports\AllDataTypesExport;
use Joy\VoyagerExport\Exports\DataTypeExport;

/**
 * Class ExportServiceProvider
 *
 * @category  Package
 * @package   JoyVoyagerExport
 * @author    Ramakant Gangwar <gangwar.ramakant@gmail.com>
 * @copyright 2021 Copyright (c) Ramakant Gangwar (https://github.com/rxcod9)
 * @license   http://github.com/rxcod9/joy-voyager-export/blob/main/LICENSE New BSD License
 * @link      https://github.com/rxcod9/joy-voyager-export
 */
class ExportServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('joy-voyager-export.export', function ($app) {
            return (new DataTypeExport());
        });

        $this->app->bind('joy-voyager-export.export-all', function ($app) {
            return (new AllDataTypesExport());
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'joy-voyager-export.export',
            'joy-voyager-export.export-all',
        ];
    }
}
