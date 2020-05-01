<?php

namespace Laurel\MultiRoute\App\Providers;

use Illuminate\Support\ServiceProvider;
use Laurel\MultiRoute\Models\Route;

class MultiRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");
        $this->mergeConfigFrom(__DIR__ . "/../../config/multi-route.php", 'multi-route');
    }
}
