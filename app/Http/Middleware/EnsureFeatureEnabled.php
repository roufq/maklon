<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        // featureKey uses dot-notation under config('features')
        $enabled = config('features.'. $featureKey);
        if ($enabled === null) {
            // Missing key defaults to true (do not block unknown)
            return $next($request);
        }
        if ($enabled === false) {
            abort(404);
        }
        return $next($request);
    }
}

