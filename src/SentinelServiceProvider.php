<?php

namespace Winex\Sentinel;

use Illuminate\Support\ServiceProvider;

class SentinelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sentinel');
        
        $this->publishes([
            __DIR__.'/../config/sentinel.php' => config_path('sentinel.php'),
        ], 'sentinel-config');
    }

    public function register()
    {
        $this->app->singleton(SentinelService::class, function ($app) {
            return new SentinelService();
        });
        
        $this->mergeConfigFrom(__DIR__.'/../config/sentinel.php', 'sentinel');
    }
}