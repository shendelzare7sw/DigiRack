<?php

namespace App\Providers;

use App\Models\SystemSetting;
use App\View\CartComposer;
use Carbon\Carbon;
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
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        // Share cart & wishlist counts to navbar
        View::composer('layouts.navigation', CartComposer::class);

        // Public business contact details are managed from the admin settings page.
        View::composer('layouts.app', function ($view): void {
            $settings = SystemSetting::query()
                ->whereIn('key', ['platform_email', 'platform_phone', 'platform_address'])
                ->pluck('value', 'key');

            $view->with('footerContact', [
                'email' => $settings->get('platform_email'),
                'phone' => $settings->get('platform_phone'),
                'address' => $settings->get('platform_address'),
            ]);
        });
    }
}
