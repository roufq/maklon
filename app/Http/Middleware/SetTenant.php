<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('features.tenancy')) {
            \App\Support\Tenancy\TenantManager::setTenant(null);
            return $next($request);
        }
        $tenant = null;
        // 0) From session switcher (Admin)
        if (session()->has('impersonate_tenant_id')) {
            $tenant = Tenant::find(session('impersonate_tenant_id'));
        }
        // 1) From authenticated user
        if (!$tenant && $request->user() && $request->user()->tenant_id) {
            $tenant = Tenant::find($request->user()->tenant_id);
        }
        // 2) From domain (optional)
        if (!$tenant) {
            $host = $request->getHost();
            $tenant = Tenant::where('domain', $host)->first();
        }
        TenantManager::setTenant($tenant);
        return $next($request);
    }
}
