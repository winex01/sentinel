<?php

namespace Winex\Sentinel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Winex\Sentinel\SentinelService;
use Symfony\Component\HttpFoundation\Response;

class SentinelMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!SentinelService::isOnTrial(auth()->user()) && !SentinelService::isSubscribed(auth()->user())) {
            return redirect()->route('filament.app.tenant.billing');
        }

        return $next($request);
    }
}
