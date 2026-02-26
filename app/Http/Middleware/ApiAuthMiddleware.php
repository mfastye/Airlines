<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        $apiSecret = $request->header('X-API-Secret');

        if (!$apiKey || !$apiSecret) {
            return response()->json(['success' => false, 'message' => 'API credentials required'], 401);
        }

        $agent = Agent::where('api_key', $apiKey)
            ->where('api_secret', $apiSecret)
            ->where('api_enabled', true)
            ->where('status', 'active')
            ->first();

        if (!$agent) {
            return response()->json(['success' => false, 'message' => 'Invalid API credentials'], 401);
        }

        $request->attributes->set('agent', $agent);

        return $next($request);
    }
}
