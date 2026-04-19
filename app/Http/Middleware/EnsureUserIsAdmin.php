<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('filament.admin.auth.login');
            }

            $user = Auth::user();

            if (!$user->is_admin) {
                Log::warning('Non-admin access attempt', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                abort(403, 'You do not have admin access.');
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error in admin middleware', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
