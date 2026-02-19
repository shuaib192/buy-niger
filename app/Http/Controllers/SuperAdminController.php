<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: SuperAdminController
 */

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\FeatureToggle;
use App\Models\Vendor;
use App\Models\User;
use App\Models\VendorPayout;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Dispute;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_vendors' => Vendor::where('status', 'approved')->count(),
            'pending_vendors' => Vendor::where('status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders' => Order::count(),
        ];

        $pendingVendors = Vendor::with('user')->where('status', 'pending')->latest()->take(5)->get();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'pendingVendors', 'recentOrders'));
    }

    /**
     * Display system settings.
     */
    public function settings()
    {
        $settings = SystemSetting::all()->groupBy('group');
        $features = FeatureToggle::all();
        
        return view('superadmin.settings', compact('settings', 'features'));
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Display vendor management.
     */
    public function vendors(Request $request)
    {
        $query = Vendor::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()->paginate(20);
        
        return view('superadmin.vendors.index', compact('vendors'));
    }

    /**
     * Display vendor details.
     */
    public function vendorShow(Vendor $vendor)
    {
        $vendor->load(['user', 'products', 'orders']);
        return view('superadmin.vendors.show', compact('vendor'));
    }

    /**
     * Display user details.
     */
    public function userShow(User $user)
    {
        $user->load(['role', 'orders', 'addresses']);
        return view('superadmin.users.show', compact('user'));
    }

    /**
     * Approve or reject a vendor.
     */
    public function updateVendorStatus(Request $request, Vendor $vendor)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,suspended',
            'reason' => 'nullable|string'
        ]);

        $oldStatus = $vendor->status;
        $vendor->update([
            'status' => $request->status,
            'approved_at' => $request->status === 'approved' ? now() : $vendor->approved_at,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_vendor_status',
            'model_type' => 'Vendor',
            'model_id' => $vendor->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $request->status, 'reason' => $request->reason],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send notification + email to vendor
        $user = $vendor->user;
        $this->sendVendorStatusNotification($user, $vendor, $request->status, $request->reason);

        return back()->with('success', "Vendor status updated to {$request->status}. Notification & email sent.");
    }

    /**
     * Approve or reject vendor KYC.
     */
    public function updateKycStatus(Request $request, Vendor $vendor)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldKyc = $vendor->kyc_status;

        $vendor->update([
            'kyc_status' => $request->status,
            'kyc_verified_at' => $request->status === 'verified' ? now() : null,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_kyc_status',
            'model_type' => 'Vendor',
            'model_id' => $vendor->id,
            'old_values' => ['kyc_status' => $oldKyc],
            'new_values' => ['kyc_status' => $request->status, 'reason' => $request->reason],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send in-app notification
        $user = $vendor->user;
        if ($request->status === 'verified') {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'kyc_verified',
                'title' => '🎉 KYC Verified!',
                'message' => 'Congratulations! Your identity has been verified. You can now publish products and receive payouts.',
                'action_url' => route('vendor.dashboard'),
            ]);
        } else {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'kyc_rejected',
                'title' => '⚠️ KYC Rejected',
                'message' => 'Your KYC verification was rejected. Reason: ' . ($request->reason ?? 'Not specified') . '. Please re-submit your documents.',
                'action_url' => route('vendor.settings'),
            ]);
        }

        // Send email notification
        $this->sendKycEmail($user, $vendor, $request->status, $request->reason);

        $label = $request->status === 'verified' ? 'verified' : 'rejected';
        return back()->with('success', "Vendor KYC has been {$label}. Notification & email sent.");
    }

    /**
     * Send KYC status email to vendor.
     */
    protected function sendKycEmail($user, $vendor, $status, $reason = null)
    {
        try {
            if ($status === 'verified') {
                $subject = 'BuyNiger — Your KYC Has Been Verified! ✅';
                $iconBg = '#22c55e';
                $icon = '✓';
                $heading = 'Identity Verified!';
                $bodyText = "Great news, <strong>{$user->name}</strong>! Your identity verification (KYC) for <strong>{$vendor->store_name}</strong> has been approved. You now have full access to publish products and receive payouts.";
                $ctaText = 'Go to Dashboard';
                $ctaUrl = route('vendor.dashboard');
                $ctaBg = '#22c55e';
            } else {
                $subject = 'BuyNiger — KYC Verification Update';
                $iconBg = '#ef4444';
                $icon = '✗';
                $heading = 'KYC Not Approved';
                $reasonText = $reason ? "<br><br><strong>Reason:</strong> {$reason}" : '';
                $bodyText = "Hello <strong>{$user->name}</strong>, unfortunately your KYC verification for <strong>{$vendor->store_name}</strong> was not approved at this time.{$reasonText}<br><br>Please review your documents and re-submit them.";
                $ctaText = 'Update KYC Documents';
                $ctaUrl = route('vendor.settings');
                $ctaBg = '#ef4444';
            }

            $emailBody = '
            <div style="font-family:\'Segoe UI\',Roboto,sans-serif;max-width:480px;margin:0 auto;padding:40px 20px;">
                <div style="text-align:center;margin-bottom:32px;">
                    <div style="width:64px;height:64px;background:'.$iconBg.';border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:white;font-size:28px;font-weight:bold;">'.$icon.'</span>
                    </div>
                    <h2 style="margin:0;color:#1e293b;font-size:22px;">'.$heading.'</h2>
                </div>
                <p style="color:#475569;font-size:15px;line-height:1.7;">'.$bodyText.'</p>
                <div style="text-align:center;margin:32px 0;">
                    <a href="'.$ctaUrl.'" style="display:inline-block;background:'.$ctaBg.';color:white;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;">'.$ctaText.'</a>
                </div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:32px 0;">
                <p style="color:#94a3b8;font-size:12px;text-align:center;">BuyNiger — Multi-Vendor Marketplace</p>
            </div>';

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $subject, $emailBody) {
                $message->to($user->email)
                    ->subject($subject)
                    ->html($emailBody);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('KYC email failed: ' . $e->getMessage());
        }
    }

    /**
     * Send vendor status notification + email.
     */
    protected function sendVendorStatusNotification($user, $vendor, $status, $reason = null)
    {
        // In-app notification
        $notifData = match($status) {
            'approved' => [
                'type' => 'vendor_approved',
                'title' => '🎉 Store Approved!',
                'message' => "Congratulations! Your store \"{$vendor->store_name}\" has been approved. You can now start listing products and selling on BuyNiger.",
                'url' => route('vendor.dashboard'),
            ],
            'rejected' => [
                'type' => 'vendor_rejected',
                'title' => '❌ Store Application Rejected',
                'message' => 'Your vendor application was not approved.' . ($reason ? " Reason: {$reason}" : '') . ' Please contact support for more details.',
                'url' => route('contact'),
            ],
            'suspended' => [
                'type' => 'vendor_suspended',
                'title' => '⚠️ Store Suspended',
                'message' => 'Your store has been suspended.' . ($reason ? " Reason: {$reason}" : '') . ' Please contact support to resolve this.',
                'url' => route('contact'),
            ],
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => $notifData['type'],
            'title' => $notifData['title'],
            'message' => $notifData['message'],
            'action_url' => $notifData['url'],
        ]);

        // Email
        try {
            $emailConfig = match($status) {
                'approved' => [
                    'subject' => 'BuyNiger — Your Store Has Been Approved! 🎉',
                    'iconBg' => '#22c55e', 'icon' => '✓',
                    'heading' => 'Store Approved!',
                    'body' => "Great news, <strong>{$user->name}</strong>! Your store <strong>{$vendor->store_name}</strong> has been approved. You can now start listing products and selling on BuyNiger.",
                    'ctaText' => 'Go to Dashboard', 'ctaUrl' => route('vendor.dashboard'), 'ctaBg' => '#22c55e',
                ],
                'rejected' => [
                    'subject' => 'BuyNiger — Vendor Application Update',
                    'iconBg' => '#ef4444', 'icon' => '✗',
                    'heading' => 'Application Not Approved',
                    'body' => "Hello <strong>{$user->name}</strong>, unfortunately your vendor application for <strong>{$vendor->store_name}</strong> was not approved." . ($reason ? "<br><br><strong>Reason:</strong> {$reason}" : '') . '<br><br>Please contact our support team for more details.',
                    'ctaText' => 'Contact Support', 'ctaUrl' => route('contact'), 'ctaBg' => '#ef4444',
                ],
                'suspended' => [
                    'subject' => 'BuyNiger — Store Suspension Notice',
                    'iconBg' => '#f59e0b', 'icon' => '⚠',
                    'heading' => 'Store Suspended',
                    'body' => "Hello <strong>{$user->name}</strong>, your store <strong>{$vendor->store_name}</strong> has been suspended." . ($reason ? "<br><br><strong>Reason:</strong> {$reason}" : '') . '<br><br>Please contact our support team to resolve this.',
                    'ctaText' => 'Contact Support', 'ctaUrl' => route('contact'), 'ctaBg' => '#f59e0b',
                ],
            };

            $emailBody = '
            <div style="font-family:Segoe UI,Roboto,sans-serif;max-width:480px;margin:0 auto;padding:40px 20px;">
                <div style="text-align:center;margin-bottom:32px;">
                    <div style="width:64px;height:64px;background:'.$emailConfig['iconBg'].';border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:white;font-size:28px;font-weight:bold;">'.$emailConfig['icon'].'</span>
                    </div>
                    <h2 style="margin:0;color:#1e293b;font-size:22px;">'.$emailConfig['heading'].'</h2>
                </div>
                <p style="color:#475569;font-size:15px;line-height:1.7;">'.$emailConfig['body'].'</p>
                <div style="text-align:center;margin:32px 0;">
                    <a href="'.$emailConfig['ctaUrl'].'" style="display:inline-block;background:'.$emailConfig['ctaBg'].';color:white;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;">'.$emailConfig['ctaText'].'</a>
                </div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:32px 0;">
                <p style="color:#94a3b8;font-size:12px;text-align:center;">BuyNiger — Multi-Vendor Marketplace</p>
            </div>';

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $emailConfig, $emailBody) {
                $message->to($user->email)
                    ->subject($emailConfig['subject'])
                    ->html($emailBody);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Vendor status email failed: ' . $e->getMessage());
        }
    }

    /**
     * Display AI control panel.
     */
    public function aiControl()
    {
        // Placeholder for AI settings
        return view('superadmin.ai.index');
    }

    /**
     * Display all orders.
     */
    public function orders(Request $request)
    {
        $query = Order::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);
        
        return view('superadmin.orders.index', compact('orders'));
    }

    /**
     * Display order detail.
     */
    public function orderShow(Order $order)
    {
        $order->load(['user', 'items.product', 'items.vendor']);
        return view('superadmin.orders.show', compact('order'));
    }

    /**
     * Update order status (Admin Override).
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_update_order_status',
            'model_type' => 'Order',
            'model_id' => $order->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $request->status, 'notes' => $request->notes],
            'ip_address' => $request->ip(),
        ]);

    }

    /**
     * List all payout requests.
     */
    public function payouts(Request $request)
    {
        $query = VendorPayout::with('vendor.user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payouts = $query->latest()->paginate(20);
        
        return view('superadmin.payouts.index', compact('payouts'));
    }

    /**
     * Update payout status.
     */
    public function updatePayoutStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:processing,completed,failed',
            'notes' => 'nullable|string'
        ]);

        $payout = VendorPayout::findOrFail($id);
        $oldStatus = $payout->status;
        
        $payout->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'processed_at' => in_array($request->status, ['completed', 'failed']) ? now() : $payout->processed_at
        ]);

        // If failed, refund vendor balance
        if ($request->status == 'failed' && $oldStatus != 'failed') {
            $payout->vendor->increment('balance', $payout->amount);
        }

        return back()->with('success', "Payout status updated to {$request->status}.");
    }

    // ==================== USER MANAGEMENT ====================

    /**
     * List all users.
     */
    public function users(Request $request)
    {
        $query = User::with('role');

        // Hide Super Admins from non-Super Admins
        if (auth()->user()->role_id !== 1) {
            $query->where('role_id', '!=', 1);
        }

        if ($request->role) {
            $query->where('role_id', $request->role);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(20);

        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Toggle user ban status.
     */
    public function toggleUserBan(User $user)
    {
        // Prevent banning Super Admins
        if ($user->role_id === 1) {
            return back()->with('error', "Super Admin accounts cannot be banned.");
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'banned';
        return back()->with('success', "User has been {$action}.");
    }

    // ==================== PRODUCT MODERATION ====================

    /**
     * List all products.
     */
    public function products(Request $request)
    {
        $query = Product::with(['vendor', 'category']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(20);

        return view('superadmin.products.index', compact('products'));
    }

    /**
     * Update product status.
     */
    public function updateProductStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:active,draft,rejected,inactive,out_of_stock',
        ]);

        $status = $request->status;
        
        // Map 'rejected' and 'archived' to valid database enums
        if ($status === 'rejected' || $status === 'archived') {
            $status = 'inactive';
        }

        $product->update(['status' => $status]);

        return back()->with('success', "Product status updated to {$status}.");
    }

    /**
     * Toggle product feature status.
     */
    public function toggleProductFeature(Product $product)
    {
        $product->is_featured = !$product->is_featured;
        $product->save();

        $status = $product->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Product is now {$status}.");
    }

    // ==================== DISPUTE MANAGEMENT ====================

    /**
     * List disputes.
     */
    public function disputes(Request $request)
    {
        $query = Dispute::with(['user', 'order']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $disputes = $query->latest()->paginate(20);

        return view('superadmin.disputes.index', compact('disputes'));
    }

    /**
     * Update dispute status.
     */
    public function updateDisputeStatus(Request $request, Dispute $dispute)
    {
        $request->validate([
            'status' => 'required|string',
            'resolution_notes' => 'nullable|string'
        ]);

        $timestamp = now()->format('Y-m-d H:i');
        $user = auth()->user()->name;
        $newNote = $request->resolution_notes ? "[{$timestamp} by {$user}]: {$request->resolution_notes}\n" : '';
        
        $dispute->update([
            'status' => $request->status,
            'resolution_notes' => $dispute->resolution_notes . $newNote
        ]);

        return back()->with('success', "Dispute updated successfully.");
    }

    /**
     * Display analytics page.
     */
    public function analytics()
    {
        return view('superadmin.analytics.index'); 
    }

    /**
     * Display user roles management.
     */
    public function userRoles()
    {
        return view('superadmin.users.roles'); 
    }

    /**
     * Display payment settings.
     */
    public function paymentSettings()
    {
        return view('superadmin.payments.index');
    }

    /**
     * Display email settings.
     */
    public function emailSettings()
    {
        return view('superadmin.email.index');
    }

    /**
     * Display AI settings.
     */
    public function aiSettings()
    {
        return view('superadmin.ai.settings');
    }

    /**
     * Update AI settings.
     */
    public function updateAiSettings(Request $request)
    {
        $settings = $request->input('settings', []);
        
        // Gemini
        if (!empty($settings['ai_gemini_key']) && $settings['ai_gemini_key'] !== '••••••••••') {
            \App\Models\AIProvider::updateOrCreate(
                ['name' => 'gemini'],
                [
                    'display_name' => 'Google Gemini',
                    'credentials' => ['api_key' => $settings['ai_gemini_key']],
                    'model' => $settings['ai_gemini_model'] ?? 'gemini-pro',
                    'is_active' => isset($settings['ai_gemini_active']),
                    'priority' => 2
                ]
            );
        } elseif (isset($settings['ai_gemini_active'])) {
            \App\Models\AIProvider::where('name', 'gemini')->update(['is_active' => true]);
        } else {
            \App\Models\AIProvider::where('name', 'gemini')->update(['is_active' => false]);
        }

        // OpenAI
        if (!empty($settings['ai_openai_key']) && $settings['ai_openai_key'] !== '••••••••••') {
            \App\Models\AIProvider::updateOrCreate(
                ['name' => 'openai'],
                [
                    'display_name' => 'OpenAI GPT-4',
                    'credentials' => ['api_key' => $settings['ai_openai_key']],
                    'model' => 'gpt-4',
                    'is_active' => isset($settings['ai_openai_active']),
                    'priority' => 1
                ]
            );
        } elseif (isset($settings['ai_openai_active'])) {
            \App\Models\AIProvider::where('name', 'openai')->update(['is_active' => true]);
        } else {
            \App\Models\AIProvider::where('name', 'openai')->update(['is_active' => false]);
        }

        // Groq
        if (!empty($settings['ai_groq_key']) && $settings['ai_groq_key'] !== '••••••••••') {
            \App\Models\AIProvider::updateOrCreate(
                ['name' => 'groq'],
                [
                    'display_name' => 'Groq Llama',
                    'credentials' => ['api_key' => $settings['ai_groq_key']],
                    'model' => $settings['ai_groq_model'] ?? 'llama-3.3-70b-versatile',
                    'is_active' => isset($settings['ai_groq_active']),
                    'priority' => 3
                ]
            );
        } elseif (isset($settings['ai_groq_active'])) {
            \App\Models\AIProvider::where('name', 'groq')->update(['is_active' => true]);
        } else {
            \App\Models\AIProvider::where('name', 'groq')->update(['is_active' => false]);
        }

        return back()->with('success', 'AI Settings saved successfully!');
    }

    /**
     * Toggle AI Emergency Kill Switch.
     */
    public function toggleAiKillSwitch(Request $request)
    {
        $status = \App\Models\AIEmergencyStatus::firstOrCreate([]);
        $status->kill_switch_enabled = !$status->kill_switch_enabled;
        $status->triggered_by = auth()->id();
        $status->triggered_at = now();
        $status->save();

        $msg = $status->kill_switch_enabled ? 'EMERGENCY KILL SWITCH ACTIVATED. All AI systems disabled.' : 'AI Systems Restored.';
        $type = $status->kill_switch_enabled ? 'error' : 'success';

        return back()->with($type, $msg);
    }

    /**
     * Display audit logs.
     */
    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('superadmin.audit.index', compact('logs'));
    }

    public function messages()
    {
        $messages = \App\Models\ContactMessage::latest()->paginate(15);
        return view('superadmin.messages.index', compact('messages'));
    }

    public function transactions()
    {
        // Fetch completed orders (income) - eager load relationships to avoid N+1
        $orders = \App\Models\Order::where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->with('user') 
            ->get()
            ->map(function ($order) {
                return (object)[
                    'id' => $order->id,
                    'type' => 'income',
                    'reference' => 'Order #' . $order->order_number,
                    'amount' => $order->total,
                    'status' => 'completed',
                    'date' => $order->created_at,
                    'user' => $order->user ? $order->user->name : 'Unknown User', // Handle potential null user
                    'description' => 'Payment from customer',
                    'related_model' => $order
                ];
            });

        // Fetch vendor payouts (expense)
        $payouts = \App\Models\VendorPayout::where('status', 'completed')
            ->orderBy('processed_at', 'desc')
            ->with('vendor')
            ->get()
            ->map(function ($payout) {
                return (object)[
                    'id' => $payout->id,
                    'type' => 'expense',
                    'reference' => 'Payout #' . ($payout->reference ?? 'N/A'),
                    'amount' => $payout->amount,
                    'status' => 'completed',
                    'date' => $payout->processed_at,
                    'user' => $payout->vendor ? $payout->vendor->store_name : 'Unknown Vendor',
                    'description' => 'Payout to vendor',
                    'related_model' => $payout
                ];
            });

        // Merge and sort
        $transactions = $orders->concat($payouts)->sortByDesc('date');
        
        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 20;
        $sliced = $transactions->slice(($page - 1) * $perPage, $perPage)->values();
        
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $transactions->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('superadmin.transactions.index', compact('paginatedTransactions'));
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $search = $request->order_number;

        // Search by Order Number OR Tracking ID (inside JSON)
        $order = \App\Models\Order::where('order_number', $search)
            ->orWhere('shipping_address->tracking_id', $search)
            ->first();

        if ($order) {
            // Determine prefix based on route
            $prefix = request()->is('admin/*') ? 'admin.' : 'superadmin.';
            return redirect()->route($prefix . 'orders.show', $order->id);
        }

        return back()->with('error', 'Order not found with that ID or Tracking Number.');
    }
}
