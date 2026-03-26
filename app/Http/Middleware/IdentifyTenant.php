<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check Postman/API Header first
        $tenantId = $request->header('X-Tenant-ID');

        // 2. If no header, check the Browser Session
        if (!$tenantId) {
            $tenantId = session('tenant_id');
        }

        // 3. If still nothing, but user is logged in, grab it from the User record
        // We use Auth:: instead of auth() for consistency with your imports
        if (!$tenantId && Auth::check()) {
            $tenantId = Auth::user()->tenant_id;
            session(['tenant_id' => $tenantId]);
        }

        // 4. Save to config so the Global Scope (TenantScope) can find it
        if ($tenantId) {
            config(['app.tenant_id' => $tenantId]);
        }

        // 5. SECURITY: Only block if we aren't on a login/public page
        if (!$tenantId && !$request->is('login*', 'logout*', 'api/login*')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}