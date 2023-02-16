<?php

namespace Masoudi\NovaAcl\Providers;

use Illuminate\Support\ServiceProvider;
use Masoudi\NovaAcl\Console\MakeOwner;
use Masoudi\NovaAcl\Console\Reload;
use Masoudi\NovaAcl\Console\Translate;

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . "/../../export/lang", 'nova-acl');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . "/../../export/database/migrations/2023_01_04_100528_add_acl_columns_on_spatie_permissions_table.php"
                => database_path(sprintf("migrations/%s_add_acl_columns_on_spatie_permissions_table.php", date("Y_m_d_His"))),
                __DIR__ . "/../../export/class/resources" => app_path("Nova"),
                __DIR__ . '/../../export/lang' => $this->app->langPath('vendor/nova-acl'),
            ], 'nova-acl');

            $this->commands([
                Reload::class,
                MakeOwner::class,
            ]);
        }

    }
}