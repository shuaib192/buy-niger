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

            // Check email verification
            if (!$user->email_verified_at) {
                Auth::logout();
                // Send a fresh OTP
                $this->sendVerificationOtp($user);
                return redirect()->route('verification.show', ['email' => $user->email])
                    ->with('info', 'Please verify your email address first. A new code has been sent.');
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

        // Send verification OTP
        $this->sendVerificationOtp($user);

        return redirect()->route('verification.show', ['email' => $user->email])
            ->with('success', 'Account created successfully! Please check your email for the verification code.');
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

        // Generate clean store slug
        $baseSlug = Str::slug($request->store_name);
        $slug = $baseSlug;
        $count = 1;
        while (Vendor::where('store_slug', $slug)->exists()) {
            $count++;
            $slug = $baseSlug . '-' . $count;
        }

        // Create vendor profile
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_slug' => $slug,
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

        // Send verification OTP
        $this->sendVerificationOtp($user);

        return redirect()->route('verification.show', ['email' => $user->email])
            ->with('success', 'Vendor account created successfully! 🎉 Please verify your email first. Once verified, your store application will be reviewed and approved within 2 business days.');
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
     * Handle forgot password request — generate OTP and send via email
     */
    public function forgotPassword(Request $request)
    {
        $method = $request->input('method', 'email');

        if ($method === 'whatsapp') {
            return back()->with('info', 'WhatsApp reset is coming soon! Please use the email option for now.')->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with that email address.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP (using password_reset_tokens table)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Send OTP via email
        try {
            $user = User::where('email', $email)->first();
            $emailBody = '
<div style="max-width:600px;margin:0 auto;font-family:Inter,Arial,sans-serif;background:#f8fafc;">
    <div style="background:linear-gradient(135deg,#0f172a,#1e40af);padding:36px 32px;text-align:center;border-radius:0 0 24px 24px;">
        <h1 style="color:white;font-size:26px;margin:0 0 4px;">Buy<span style="color:#60a5fa;">Niger</span></h1>
        <p style="color:rgba(255,255,255,0.6);font-size:12px;margin:0;">Password Reset</p>
    </div>
    <div style="padding:36px 32px;text-align:center;">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:50%;margin:0 auto 18px;display:flex;align-items:center;justify-content:center;">
            <span style="color:white;font-size:26px;">🔑</span>
        </div>
        <h2 style="font-size:20px;color:#1e293b;margin:0 0 8px;">Reset Your Password</h2>
        <p style="color:#64748b;font-size:14px;margin:0 0 24px;">Hi ' . e($user->name) . ', use this code to reset your password:</p>
        <div style="background:#1e293b;border-radius:14px;padding:20px;margin-bottom:24px;">
            <span style="font-size:36px;font-weight:800;color:#60a5fa;letter-spacing:8px;">' . $otp . '</span>
        </div>
        <p style="color:#94a3b8;font-size:13px;margin:0;">This code expires in <strong>15 minutes</strong>. Do not share it with anyone.</p>
    </div>
    <div style="text-align:center;padding:20px;border-top:1px solid #e2e8f0;">
        <p style="color:#94a3b8;font-size:11px;margin:0;">If you did not request this, please ignore this email.</p>
    </div>
</div>';

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($email, $emailBody) {
                $message->to($email)
                    ->subject('BuyNiger — Your Password Reset Code')
                    ->html($emailBody);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password reset email failed: ' . $e->getMessage());
        }

        return redirect()->route('password.reset', ['email' => $email])
            ->with('success', 'A 6-digit OTP has been sent to your email. Check your inbox (and spam folder).');
    }

    /**
     * Show reset password page (OTP + new password)
     */
    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->email]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
        ], [
            'otp.size' => 'OTP must be exactly 6 digits.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(['email' => $request->email]);
        }

        // Verify OTP
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['otp' => 'No reset request found. Please request a new OTP.'])->withInput(['email' => $request->email]);
        }

        // Check expiry (15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.'])->withInput(['email' => $request->email]);
        }

        // Verify OTP hash
        if (!Hash::check($request->otp, $resetRecord->token)) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.'])->withInput(['email' => $request->email]);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully! Please log in with your new password.');
    }

    /**
     * Send verification OTP to user's email
     */
    protected function sendVerificationOtp(User $user): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in password_reset_tokens with type prefix
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'verify_' . $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            $emailBody = '
<div style="max-width:600px;margin:0 auto;font-family:Inter,Arial,sans-serif;background:#f8fafc;">
    <div style="background:linear-gradient(135deg,#0f172a,#1e40af);padding:36px 32px;text-align:center;border-radius:0 0 24px 24px;">
        <h1 style="color:white;font-size:26px;margin:0 0 4px;">Buy<span style="color:#60a5fa;">Niger</span></h1>
        <p style="color:rgba(255,255,255,0.6);font-size:12px;margin:0;">Email Verification</p>
    </div>
    <div style="padding:36px 32px;text-align:center;">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:50%;margin:0 auto 18px;display:flex;align-items:center;justify-content:center;">
            <span style="color:white;font-size:26px;">✉️</span>
        </div>
        <h2 style="font-size:20px;color:#1e293b;margin:0 0 8px;">Verify Your Email</h2>
        <p style="color:#64748b;font-size:14px;margin:0 0 24px;">Hi ' . e($user->name) . ', welcome to BuyNiger! Enter this code to verify your email:</p>
        <div style="background:#1e293b;border-radius:14px;padding:20px;margin-bottom:24px;">
            <span style="font-size:36px;font-weight:800;color:#60a5fa;letter-spacing:8px;">' . $otp . '</span>
        </div>
        <p style="color:#94a3b8;font-size:13px;margin:0;">This code expires in <strong>15 minutes</strong>. Do not share it with anyone.</p>
    </div>
    <div style="text-align:center;padding:20px;border-top:1px solid #e2e8f0;">
        <p style="color:#94a3b8;font-size:11px;margin:0;">If you did not create this account, please ignore this email.</p>
    </div>
</div>';

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $emailBody) {
                $message->to($user->email)
                    ->subject('BuyNiger — Verify Your Email Address')
                    ->html($emailBody);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Verification email failed: ' . $e->getMessage());
        }
    }

    /**
     * Show email verification page
     */
    public function showVerifyEmail(Request $request)
    {
        return view('auth.verify-email', ['email' => $request->email]);
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ], [
            'otp.size' => 'Code must be exactly 6 digits.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'verify_' . $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['otp' => 'No verification code found. Please request a new one.'])->withInput();
        }

        // Check expiry (15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', 'verify_' . $request->email)->delete();
            return back()->withErrors(['otp' => 'Code has expired. Please request a new one.'])->withInput();
        }

        // Verify OTP
        if (!Hash::check($request->otp, $resetRecord->token)) {
            return back()->withErrors(['otp' => 'Invalid code. Please try again.'])->withInput();
        }

        // Mark email as verified
        $user = User::where('email', $request->email)->first();
        $user->update(['email_verified_at' => now()]);

        // Delete used token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', 'verify_' . $request->email)->delete();

        // Log user in
        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        return $this->redirectToDashboard()->with('success', '🎉 Email verified! Welcome to BuyNiger!');
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('info', 'Email is already verified. You can log in.');
        }

        $this->sendVerificationOtp($user);

        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}

