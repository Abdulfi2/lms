<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Get from user settings if logged in
            if (auth()->check() && auth()->user()->settings) {
                App::setLocale(auth()->user()->settings->language);
                Session::put('locale', auth()->user()->settings->language);
            } else {
                App::setLocale(config('app.locale'));
            }
        }

        return $next($request);
    }
}