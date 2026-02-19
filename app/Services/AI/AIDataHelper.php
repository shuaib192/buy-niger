<?php
/**
 * BuyNiger AI Database Helper - Full Data Context
 * Provides comprehensive data to AI so it can answer ANY question
 */

namespace App\Services\AI;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\VendorPayout;
use App\Models\OrderItem;

class AIDataHelper
{
    protected $user;
    protected $role;
    protected $vendor;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->role = $this->detectRole();
        $this->vendor = $user->vendor ?? null;
    }

    protected function detectRole(): string
    {
        if (method_exists($this->user, 'isSuperAdmin') && $this->user->isSuperAdmin()) return 'superadmin';
        if (method_exists($this->user, 'isAdmin') && $this->user->isAdmin()) return 'admin';
        if (method_exists($this->user, 'isVendor') && $this->user->isVendor()) return 'vendor';
        return 'customer';
    }

    /**
     * Get COMPREHENSIVE context with ACTUAL data for AI
     */
    public function getFullContext(): string
    {
        $ctx = "Name: {$this->user->name}\nRole: {$this->role}\n";

        try {
            switch ($this->role) {
                case 'vendor':
                    $ctx .= $this->getVendorFullContext();
                    break;
                case 'admin':
                case 'superadmin':
                    $ctx .= $this->getAdminFullContext();
                    break;
                case 'customer':
                    $ctx .= $this->getCustomerFullContext();
                    break;
            }
        } catch (\Exception $e) {
            $ctx .= "Error loading data: " . $e->getMessage();
        }

        return $ctx;
    }

    /**
     * Get all vendor data with actual product names
     */
    protected function getVendorFullContext(): string
    {
        if (!$this->vendor) return "No vendor profile.";

        $ctx = "Store: {$this->vendor->store_name}\n";
        $ctx .= "Balance: N" . number_format($this->vendor->balance ?? 0) . "\n\n";

        // Get ALL products with names
        $products = Product::where('vendor_id', $this->vendor->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get(['id', 'name', 'price', 'quantity', 'status']);

        $ctx .= "YOUR PRODUCTS (" . Product::where('vendor_id', $this->vendor->id)->count() . " total):\n";
        foreach ($products as $p) {
            $stock = $p->quantity < 5 ? " LOW STOCK" : "";
            $ctx .= "- #{$p->id}: {$p->name} | N" . number_format($p->price) . " | {$p->quantity} in stock{$stock}\n";
        }

        // Get recent orders
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->latest()
            ->take(10)
            ->with(['items' => fn($q) => $q->where('vendor_id', $this->vendor->id)->with('product'), 'user'])
            ->get();

        $pendingCount = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('status', 'pending')->count();
        
        $ctx .= "\nYOUR ORDERS ({$pendingCount} pending):\n";
        foreach ($orders as $o) {
            $items = $o->items->map(fn($i) => $i->product?->name ?? 'Item')->implode(', ');
            $ctx .= "- {$o->order_number}: {$items} | N" . number_format($o->total) . " | " . ucfirst($o->status) . "\n";
        }

        // Recent sales
        $totalRevenue = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('payment_status', 'paid')
            ->sum('total');
        $todayRevenue = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $ctx .= "\nFINANCIALS:\n";
        $ctx .= "- Total Revenue: N" . number_format($totalRevenue) . "\n";
        $ctx .= "- Today: N" . number_format($todayRevenue) . "\n";
        $ctx .= "- Available Balance: N" . number_format($this->vendor->balance ?? 0) . "\n";

        return $ctx;
    }

    /**
     * Get admin full context
     */
    protected function getAdminFullContext(): string
    {
        $ctx = "PLATFORM OVERVIEW:\n";
        $ctx .= "- Users: " . User::count() . " (today: " . User::whereDate('created_at', today())->count() . ")\n";
        $ctx .= "- Vendors: " . Vendor::count() . " (pending: " . Vendor::where('status', 'pending')->count() . ")\n";
        $ctx .= "- Products: " . Product::count() . " (active: " . Product::where('status', 'active')->count() . ")\n";
        $ctx .= "- Orders: " . Order::count() . " (today: " . Order::whereDate('created_at', today())->count() . ")\n";
        $ctx .= "- Total Revenue: N" . number_format(Order::where('payment_status', 'paid')->sum('total')) . "\n";
        $ctx .= "- Today Revenue: N" . number_format(Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total')) . "\n";

        // Pending vendors
        $pendingVendors = Vendor::where('status', 'pending')->take(5)->get(['id', 'store_name']);
        if ($pendingVendors->isNotEmpty()) {
            $ctx .= "\nPENDING VENDORS:\n";
            foreach ($pendingVendors as $v) {
                $ctx .= "- #{$v->id}: {$v->store_name}\n";
            }
        }

        // Pending payouts
        $pendingPayouts = VendorPayout::where('status', 'pending')->with('vendor')->take(5)->get();
        if ($pendingPayouts->isNotEmpty()) {
            $ctx .= "\nPENDING PAYOUTS:\n";
            foreach ($pendingPayouts as $p) {
                $ctx .= "- #{$p->id}: N" . number_format($p->amount) . " ({$p->vendor->store_name})\n";
            }
        }

        // Recent orders
        $recentOrders = Order::latest()->take(5)->get(['order_number', 'total', 'status']);
        $ctx .= "\nRECENT ORDERS:\n";
        foreach ($recentOrders as $o) {
            $ctx .= "- {$o->order_number}: N" . number_format($o->total) . " | " . ucfirst($o->status) . "\n";
        }

        return $ctx;
    }

    /**
     * Get customer full context
     */
    protected function getCustomerFullContext(): string
    {
        $ctx = "";

        // Customer's orders
        $orders = Order::where('user_id', $this->user->id)
            ->latest()
            ->take(10)
            ->with(['items.product'])
            ->get();

        $ctx .= "YOUR ORDERS:\n";
        if ($orders->isEmpty()) {
            $ctx .= "No orders yet.\n";
        } else {
            foreach ($orders as $o) {
                $items = $o->items->map(fn($i) => $i->product?->name ?? 'Item')->implode(', ');
                $ctx .= "- {$o->order_number}: {$items} | N" . number_format($o->total) . " | " . ucfirst($o->status) . "\n";
            }
        }

        // Total spent
        $totalSpent = Order::where('user_id', $this->user->id)->where('payment_status', 'paid')->sum('total');
        $ctx .= "\nTotal spent: N" . number_format($totalSpent) . "\n";

        return $ctx;
    }
}
