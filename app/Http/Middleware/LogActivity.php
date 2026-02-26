<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $request->isMethod('post')) {
            UserActivityLog::log(
                auth()->id(),
                $request->route()->getName() ?? $request->path(),
                $request->method() . ' ' . $request->path(),
                ['ip' => $request->ip()]
            );
        }

        return $response;
    }
}
