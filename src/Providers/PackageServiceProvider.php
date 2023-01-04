<?php

namespace Masoudi\NovaAcl\Providers;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . "/../../export/database/migrations" => database_path("migrations"),
                __DIR__ . "/../../export/class/resources" => app_path("Nova")
            ], 'nova-acl');
        }
    }
}