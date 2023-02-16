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
                __DIR__ . "/../../export/database/migrations" => database_path("migrations"),
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