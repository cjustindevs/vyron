<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Render serves the app over HTTPS; force secure URLs for assets,
        // forms and route generation even if APP_URL is set to http://...
        if ($this->app->isProduction()) {
            URL::forceScheme('https');

            if (str_starts_with(config('app.url'), 'http://')) {
                URL::forceRootUrl(str_replace('http://', 'https://', config('app.url')));
            }
        }
    }
}
