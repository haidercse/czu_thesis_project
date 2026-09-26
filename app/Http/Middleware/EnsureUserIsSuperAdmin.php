<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || (!$user->is_admin && !$user->hasRole('Super Admin'))) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }
}