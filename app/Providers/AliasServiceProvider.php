<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AliasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Get the AliasLoader instance
//        $loader = AliasLoader::getInstance();
//
//        // Add your aliases
//        $loader->alias('Setting', \App\Facades\Setting::class);
//        $loader->alias('HelperMethods', \App\Facades\HelperMethods::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
