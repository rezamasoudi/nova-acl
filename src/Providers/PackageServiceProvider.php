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
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . "/../../export/database/migrations" => database_path("migrations"),
                __DIR__ . "/../../export/class/resources" => app_path("Nova")
            ], 'nova-acl');

            $this->commands([
                Translate::class,
                Reload::class,
                MakeOwner::class,
            ]);
        }
    }
}