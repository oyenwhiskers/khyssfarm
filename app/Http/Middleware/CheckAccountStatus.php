<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip this middleware for the pending page and logout to avoid redirect loops
        if ($request->routeIs('account.pending') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // If user status is pending, redirect to pending review page
            if ($user->isPending()) {
                return redirect()->route('account.pending');
            }

            // If user status is inactive, logout and show error
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your account has been deactivated.');
            }
        }

        return $next($request);
    }
}
