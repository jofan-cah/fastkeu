<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature, string $action): Response
    {
        // Check if user has permission
        if (!auth()->user()->hasPermission($feature, $action)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
