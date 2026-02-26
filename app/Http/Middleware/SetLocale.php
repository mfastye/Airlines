<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'ar'));

        if ($request->has('lang')) {
            $locale = in_array($request->get('lang'), ['ar', 'en']) ? $request->get('lang') : 'ar';
            session(['locale' => $locale]);
        }

        if (auth()->check() && auth()->user()->language) {
            $locale = auth()->user()->language;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
