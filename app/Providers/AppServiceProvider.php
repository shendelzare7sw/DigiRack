<?php

namespace App\Providers;

use App\View\CartComposer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production to prevent mixed-content browser warnings
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Set locale to Indonesian for all date/time formatting
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        // Share cart & wishlist counts to navbar
        View::composer('layouts.navigation', CartComposer::class);
    }
}
