<?php

namespace Maidomax\DatadogLogger;

use Illuminate\Support\ServiceProvider;

class DatadogLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/datadog-logger.php',
            'datadog-logger'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datadog-logger.php' => config_path('datadog-logger.php'),
            ], 'datadog-logger-config');
        }

        // Register custom logging driver
        $this->app['log']->extend('datadog', function ($app, array $config) {
            return (new DatadogLogger())($config);
        });
    }
}