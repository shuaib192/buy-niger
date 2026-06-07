<?php

/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 *
 * Controller: CustomerController
 */

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display customer dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->whereIn('status', ['pending', 'processing', 'shipped'])->count(),
            'wishlist_count' => Wishlist::where('user_id', $user->id)->count(),
            'reviews_given' => Review::where('user_id', $user->id)->count(),
        ];

        $recentOrders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('stats', 'recentOrders'));
    }

    /**
     * Display customer profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();

        return view('customer.profile', compact('user', 'addresses'));
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Display customer addresses.
     */
    public function addresses()
    {
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('customer.addresses', compact('addresses'));
    }

    /**
     * Store new address.
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
        ]);

        $address = Address::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => 'Nigeria',
            'is_default' => Address::where('user_id', Auth::id())->count() == 0,
        ]);

        return back()->with('success', 'Address added successfully!');
    }

    /**
     * Delete address.
     */
    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Address deleted successfully!');
    }

    /**
     * Set default address.
     */
    public function setDefaultAddress($id)
    {
        Address::where('user_id', Auth::id())->update(['is_default' => false]);

        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated!');
    }

    public function storeDispute(Request $request, Order $order)
    {
        // Ensure user owns order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        \App\Models\Dispute::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return back()->with('success', 'Dispute opened successfully. Our team will review it shortly.');
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Request $request, Order $order)
    {
        // Ensure user owns order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (! $order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled. Only pending, paid, or processing orders can be cancelled.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update order status
        $order->updateStatus('cancelled', 'Cancelled by customer: '.$request->reason, Auth::user());

        // Get all unique vendors from order items
        $order->load('items.vendor.user');
        $vendorUsers = $order->items
            ->pluck('vendor')
            ->unique('id')
            ->filter();

        // Send notification and email to each vendor
        foreach ($vendorUsers as $vendor) {
            // In-app notification
            \App\Models\Notification::create([
                'user_id' => $vendor->user_id,
                'type' => 'order_cancelled',
                'title' => '❌ Order Cancelled',
                'message' => "Order #{$order->order_number} has been cancelled by the customer. Reason: {$request->reason}",
                'action_url' => route('vendor.orders.show', $order->id),
            ]);

            // Email notification
            $this->sendOrderCancellationEmail($vendor, $order, $request->reason);
        }

        return back()->with('success', 'Order has been cancelled successfully. The vendor(s) have been notified.');
    }

    /**
     * Send order cancellation email to vendor.
     */
    protected function sendOrderCancellationEmail($vendor, $order, $reason)
    {
        try {
            $user = $vendor->user;
            if (! $user || ! $user->email) {
                return;
            }

            $subject = 'BuyNiger — Order Cancelled: #'.$order->order_number;

            // Build items list for the email
            $itemsHtml = '';
            foreach ($order->items->where('vendor_id', $vendor->id) as $item) {
                $itemsHtml .= '<div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">'
                    .'<strong>'.$item->product_name.'</strong>'
                    .' × '.$item->quantity
                    .' — ₦'.number_format($item->subtotal, 2)
                    .'</div>';
            }

            $emailBody = '
            <div style="font-family:Segoe UI,Roboto,sans-serif;max-width:480px;margin:0 auto;padding:40px 20px;">
                <div style="text-align:center;margin-bottom:32px;">
                    <div style="width:64px;height:64px;background:#ef4444;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:white;font-size:28px;font-weight:bold;">✗</span>
                    </div>
                    <h2 style="margin:0;color:#1e293b;font-size:22px;">Order Cancelled</h2>
                </div>
                <p style="color:#475569;font-size:15px;line-height:1.7;">
                    Hello <strong>'.$user->name.'</strong>, an order from your store <strong>'.$vendor->store_name.'</strong> has been cancelled by the customer.
                </p>
                <div style="background:#fef2f2;border-radius:12px;padding:16px;margin:16px 0;">
                    <div style="font-size:13px;color:#991b1b;font-weight:700;margin-bottom:8px;">CANCELLATION REASON</div>
                    <p style="margin:0;color:#dc2626;font-size:14px;">'.e($reason).'</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:16px;margin:16px 0;">
                    <div style="font-size:13px;color:#64748b;font-weight:700;margin-bottom:8px;">ORDER DETAILS — #'.$order->order_number.'</div>
                    '.$itemsHtml.'
                    <div style="padding-top:12px;font-weight:700;">Total: ₦'.number_format($order->total, 2).'</div>
                </div>
                <div style="text-align:center;margin:32px 0;">
                    <a href="'.route('vendor.orders.show', $order->id).'" style="display:inline-block;background:#ef4444;color:white;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;">View Order</a>
                </div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:32px 0;">
                <p style="color:#94a3b8;font-size:12px;text-align:center;">BuyNiger — Multi-Vendor Marketplace</p>
            </div>';

            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\GenericEmail($subject, $emailBody));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order cancellation email failed: '.$e->getMessage());
        }
    }
}
