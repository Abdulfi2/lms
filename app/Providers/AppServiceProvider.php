<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use App\Services\GoogleService;
use Illuminate\Support\Facades\Mail;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Mail::extend('gmail_api', function () {
            return new GmailApiTransport($this->app->make(GoogleService::class));
        });
    }
}
