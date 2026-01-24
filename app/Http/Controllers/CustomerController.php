<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: CustomerController
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Address;
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

        if (!Hash::check($request->current_password, $user->password)) {
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
}
