<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    // For now, we will look for a header called 'X-Tenant-ID'
    // In a real app, this could come from the URL or the logged-in user
    if ($request->hasHeader('X-Tenant-ID')) {
        $tenantId = $request->header('X-Tenant-ID');
        session(['tenant_id' => $tenantId]);
    }

    return $next($request);
}
}
