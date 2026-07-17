<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware('role:founder')
     *                  ->middleware('role:investor,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if (!in_array($userRole, $roles)) {
            // Redirect to appropriate dashboard if authenticated but wrong role
            if ($userRole === 'founder') {
                return redirect()->route('founder.dashboard')
                    ->with('error', 'Access denied. You do not have permission to view that page.');
            }
            if ($userRole === 'investor') {
                return redirect()->route('investor.dashboard')
                    ->with('error', 'Access denied.');
            }
            // Admin or unknown
            return redirect()->route('login')
                ->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
