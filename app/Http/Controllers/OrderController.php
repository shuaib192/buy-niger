<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: OrderController (Fresh replacement for CheckoutController)
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\ShippingMethod;
use App\Models\Coupon;
use App\Models\Vendor;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getCart()
    {
        return Cart::where('user_id', Auth::id())->first();
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $items = $cart->items()->with('product.category', 'product.vendor')->get();
        $addresses = Auth::user()->addresses ?? collect();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();
        $shippingMethods = ShippingMethod::active()->get();

        // Safe delivery fee calculation
        $vendorDeliveryFee = $items->map(function($item) {
            return $item->product->vendor ?? null;
        })->filter()->unique('id')->sum('delivery_fee');

        return view('shop.checkout', compact('cart', 'items', 'addresses', 'defaultAddress', 'shippingMethods', 'vendorDeliveryFee'));
    }

    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'new_address' => 'nullable|boolean',
            'first_name' => 'nullable|required_if:new_address,1|max:100',
            'last_name' => 'nullable|required_if:new_address,1|max:100',
            'phone' => 'nullable|required_if:new_address,1|max:20',
            'address_line_1' => 'nullable|required_if:new_address,1|max:500',
            'city' => 'nullable|required_if:new_address,1|max:100',
            'state' => 'nullable|required_if:new_address,1|max:100',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'coupon_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        DB::beginTransaction();
        try {
            // Handle address
            if ($request->new_address) {
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
                    'is_default' => Auth::user()->addresses()->count() == 0,
                ]);
            } else {
                $address = Address::findOrFail($request->address_id);
            }

            $shippingMethod = ShippingMethod::findOrFail($request->shipping_method_id);
            
            // Shipping calculation
            if (strtolower($shippingMethod->name) === 'pickup') {
                $shippingCost = 0;
            } else {
                $vendorIds = $cart->items->pluck('product.vendor_id')->unique();
                $shippingCost = Vendor::whereIn('id', $vendorIds)->sum('delivery_fee');
            }

            // Coupon logic
            $discount = 0;
            $coupon = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();
                if ($coupon && $coupon->isValid() && (!$coupon->min_spend || $cart->total >= $coupon->min_spend)) {
                    $discount = ($coupon->type === 'percentage') 
                        ? round($cart->total * ($coupon->value / 100), 2) 
                        : min($coupon->value, $cart->total);
                }
            }

            // Create Order
            $trackingId = 'TRK-' . strtoupper(Str::random(10));
            $order = Order::create([
                'order_number' => 'BN-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'shipping_method_id' => $shippingMethod->id,
                'subtotal' => $cart->total,
                'shipping_cost' => $shippingCost,
                'tax' => 0,
                'discount' => $discount,
                'coupon_code' => $coupon ? $coupon->code : null,
                'total' => $cart->total + $shippingCost - $discount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'shipping_address' => [
                    'name' => $address->first_name . ' ' . $address->last_name,
                    'phone' => $address->phone,
                    'address' => $address->address_line_1,
                    'city' => $address->city,
                    'state' => $address->state,
                    'tracking_id' => $trackingId,
                    'shipping_method' => $shippingMethod->name,
                ],
            ]);

            // Items and Stock
            $vendorItems = [];
            foreach ($cart->items as $item) {
                $price = $item->product->current_price;
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->vendor_id,
                    'product_name' => $item->product->name,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'subtotal' => $price * $item->quantity,
                    'status' => 'pending',
                    'tracking_number' => $trackingId,
                ]);

                $vendorItems[$item->product->vendor_id][] = $orderItem;
                $item->product->decrement('quantity', $item->quantity);
            }

            // Coupon recording
            if ($coupon) {
                DB::table('coupon_usages')->insert([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $coupon->increment('used_count');
            }

            $cart->items()->delete();
            DB::commit();

            // Email notifications
            try {
                Mail::to(Auth::user()->email)->send(new OrderConfirmation($order));
                foreach ($vendorItems as $vendorId => $items) {
                    $vendor = Vendor::with('user')->find($vendorId);
                    if ($vendor && $vendor->user) {
                        Mail::to($vendor->user->email)->send(new NewOrderNotification($order, $vendor, $items));
                    }
                }
            } catch (\Exception $e) { \Log::error('Order email failed: ' . $e->getMessage()); }

            return redirect()->route('payment.page', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function confirmation($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->where('user_id', Auth::id())->firstOrFail();
        return view('shop.order-confirmation', compact('order'));
    }
}
