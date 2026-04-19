<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Admin middleware check', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'is_admin' => auth()->check() ? auth()->user()->is_admin : null,
        ]);

        if (!auth()->check()) {
            abort(403, 'Not authenticated.');
        }
        Log::info('Type of is_admin', [
            'type' => gettype(auth()->user()?->is_admin),
            'value' => auth()->user()?->is_admin,
        ]);

        if (!(bool) auth()->user()->is_admin) {
            abort(403, 'User is not an admin.');
        }

        return $next($request);
    }
} 