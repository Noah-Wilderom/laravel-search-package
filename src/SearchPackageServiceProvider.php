<?php

namespace NoahWilderom\SearchPackage;

use Illuminate\Support\ServiceProvider;
use NoahWilderom\SearchPackage\Commands\SearchPackageCommand;

class SearchPackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->offerPublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/search-package.php',
            'laravel-search-package'
        );

        $this->commands([
            SearchPackageCommand::class
        ]);
    }

    public function offerPublishing(): void
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/search-package.php' => config_path('search-package.php'),
            ], 'laravel-search-package-config');
        }
    }
}