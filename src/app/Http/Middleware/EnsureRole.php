<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !$user->staff || !in_array($user->staff->staff_type, $roles)) {
            return redirect()->route('dashboard')->with('auth_required', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
