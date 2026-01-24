<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: AuthController
 * Handles all authentication: login, register, logout, password reset
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Events\UserRegistered;
use App\Events\VendorRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.'])->withInput();
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            $request->session()->regenerate();

            return $this->redirectToDashboard();
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    /**
     * Show customer registration page
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.register');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 4, // Customer
            'is_active' => true,
        ]);

        // Fire event
        event(new UserRegistered($user));

        // Auto login
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Welcome to BuyNiger! Your account has been created.');
    }

    /**
     * Show vendor registration page
     */
    public function showVendorRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.vendor-register');
    }

    /**
     * Handle vendor registration
     */
    public function vendorRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'business_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Vendor
            'is_active' => true,
        ]);

        // Create vendor profile
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_slug' => Str::slug($request->store_name) . '-' . Str::random(5),
            'store_description' => $request->store_description,
            'business_email' => $request->email,
            'business_phone' => $request->phone,
            'business_address' => $request->business_address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => 'Nigeria',
            'status' => 'pending',
        ]);

        // Fire events
        event(new UserRegistered($user));
        event(new VendorRegistered($vendor));

        // Auto login
        Auth::login($user);

        return redirect()->route('vendor.dashboard')
            ->with('info', 'Welcome! Your vendor application is pending approval. You can start setting up your store.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    /**
     * Redirect user to appropriate dashboard based on role
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        return match ($user->role_id) {
            1 => redirect()->route('superadmin.dashboard'),
            2 => redirect()->route('admin.dashboard'),
            3 => redirect()->route('vendor.dashboard'),
            default => redirect()->route('home'),
        };
    }

    /**
     * Show forgot password page
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // TODO: Send password reset email
        return back()->with('success', 'If an account exists with that email, you will receive a password reset link.');
    }
}
