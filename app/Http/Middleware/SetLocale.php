<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale')
            ?? $request->user()?->locale
            ?? config('app.locale', 'nl');

        if (in_array($locale, config('app.available_locales', ['nl']))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
