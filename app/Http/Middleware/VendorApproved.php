<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Middleware: VendorApproved
 * Checks if vendor is approved before allowing access
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isVendor()) {
            return redirect()->route('home')->with('error', 'Vendor access only.');
        }

        $vendor = $user->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.setup')->with('error', 'Please complete your vendor profile.');
        }

        // Allow pending vendors to access dashboard but restrict certain actions
        if ($vendor->status === 'rejected') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your vendor application was rejected. Reason: ' . ($vendor->rejection_reason ?? 'Not specified'));
        }

        if ($vendor->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your vendor account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
