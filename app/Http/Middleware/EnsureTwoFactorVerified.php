<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('features.two_factor')) {
            return $next($request);
        }
        $user = $request->user();
        if ($user && $user->two_factor_enabled) {
            if (!$request->session()->get('two_factor_passed')) {
                if (!$request->is('2fa*')) {
                    return redirect()->route('2fa.verify.form');
                }
            }
        }
        return $next($request);
    }
}
