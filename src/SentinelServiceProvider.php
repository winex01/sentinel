<?php

namespace Winex\Sentinel;

use Illuminate\Support\ServiceProvider;
use Winex\Sentinel\Commands\SentinelInstallCommand;

class SentinelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sentinel');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                SentinelInstallCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton(SentinelService::class, function ($app) {
            return new SentinelService();
        });
        
        $this->mergeConfigFrom(__DIR__.'/../config/sentinel.php', 'sentinel');
    }
}