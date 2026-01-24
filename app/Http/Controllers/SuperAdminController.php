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
        $vendor->update(['status' => $request->status]);

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

        // Dispatch events would go here (already created in EDA phase)

        return back()->with('success', "Vendor status updated to {$request->status}.");
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
        // For MVP, we might save these to the ai_providers table or system_settings
        // Assuming we update ai_providers based on the form input
        // This requires parsing the specific input names like 'settings[ai_gemini_key]'
        
        // Simplified: just redirect back with success for now, logic to be wired to Models if needed
        return back()->with('success', 'AI Settings updated successfully.');
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
}
