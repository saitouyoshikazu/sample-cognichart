<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (glob(app_path().'/Expand/Helper/*.php') AS $filename) {
            require_once($filename);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

}
