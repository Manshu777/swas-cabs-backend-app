<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin panel');
        }

        $user = Auth::user();

        if (!$user->is_active || !in_array($user->role, $roles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
