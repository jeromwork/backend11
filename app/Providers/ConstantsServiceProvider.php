<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConstantsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (!defined('DB_CONNECTION_DEFAULT')) {
            define('DB_CONNECTION_DEFAULT', config('database.default'));
        }
        if (!defined('DB_CONNECTION_MODX')) {
            define('DB_CONNECTION_MODX', config('database.MODX'));
        }
        // Add more constants here
    }

    public function register()
    {
        //
    }
}
