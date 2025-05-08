<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $repositories = require __DIR__ . '/../Repositories/repositories.php';
        $services = require __DIR__ . '/../Services/services.php';

        foreach ($repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }

        foreach ($services as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
