<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Middleware: RoleMiddleware
 * Checks if user has the required role to access a route
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Role names or IDs
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // Check if user's role matches any of the allowed roles
        foreach ($roles as $role) {
            // Support both role ID and role name
            if (is_numeric($role)) {
                if ($user->role_id == $role) {
                    return $next($request);
                }
            } else {
                if ($user->role && $user->role->name === $role) {
                    return $next($request);
                }
            }
        }

        // If no match, deny access
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return redirect()->route('home')->with('error', 'You do not have permission to access that page.');
    }
}
