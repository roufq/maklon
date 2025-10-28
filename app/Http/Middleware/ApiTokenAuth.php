<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = substr($auth, 7);
        $hash = hash('sha256', $token);
        $record = ApiToken::where('token_hash', $hash)->first();
        if (!$record) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Set acting user for this request only (stateless)
        Auth::setUser($record->user);
        return $next($request);
    }
}
