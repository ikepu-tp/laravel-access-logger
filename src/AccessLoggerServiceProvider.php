<?php

namespace ikepu_tp\AccessLogger;

use Illuminate\Support\ServiceProvider;

class AccessLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/access-logger.php', 'access-logger');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . "/resources/views", "AccessLogger");
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing()
    {
        if (!$this->app->runningInConsole()) return;

        $this->publishes([
            __DIR__ . '/config/access-logger.php' => base_path('config/access-logger.php'),
        ], 'AccessLogger-config');

        $this->publishView();
    }

    private function publishView(): void
    {
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/AccessLogger'),
        ], 'AccessLogger-views');
    }
}
