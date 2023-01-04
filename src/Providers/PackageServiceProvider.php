<?php

namespace Masoudi\NovaAcl\Providers;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . "/../../database/migrations" => database_path("migrations")
            ], 'nova-acl');
        }
    }
}