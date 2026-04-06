<?php

namespace App\Providers;

use App\View\CartComposer;
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
        // Share cart & wishlist counts to navbar
        View::composer('layouts.navigation', CartComposer::class);
    }
}
