<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: CheckoutController
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

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get user's cart.
     */
    private function getCart()
    {
        return Cart::where('user_id', Auth::id())->first();
    }

    /**
     * Generate unique tracking ID.
     */
    private function generateTrackingId()
    {
        return 'TRK-' . strtoupper(Str::random(10));
    }

    /**
     * Checkout page.
     */
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $items = $cart->items()->with('product.category', 'product.vendor')->get();
        $addresses = Auth::user()->addresses ?? collect();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();
        $shippingMethods = ShippingMethod::active()->get();

        // Calculate vendor delivery fee (sum of unique vendor fees in cart)
        $vendorDeliveryFee = $items->pluck('product.vendor')
            ->unique('id')
            ->sum('delivery_fee');

        return view('shop.checkout', compact('cart', 'items', 'addresses', 'defaultAddress', 'shippingMethods', 'vendorDeliveryFee'));
    }

    /**
     * Apply coupon code (AJAX).
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon code.'], 422);
        }

        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'This coupon has expired or reached its usage limit.'], 422);
        }

        $cart = $this->getCart();
        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        // Check minimum spend
        if ($coupon->min_spend && $cart->total < $coupon->min_spend) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum spend of ₦' . number_format($coupon->min_spend) . ' required for this coupon.'
            ], 422);
        }

        // Calculate discount
        if ($coupon->type === 'percentage') {
            $discount = round($cart->total * ($coupon->value / 100), 2);
        } else {
            $discount = min($coupon->value, $cart->total); // fixed amount, cap at cart total
        }

        return response()->json([
            'success' => true,
            'coupon_code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
            'message' => 'Coupon applied! You save ₦' . number_format($discount),
        ]);
    }

    /**
     * Process checkout and create order.
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

            // Generate tracking ID
            $trackingId = $this->generateTrackingId();

            // Calculate shipping cost based on method + vendor fees
            $shippingMethod = ShippingMethod::findOrFail($request->shipping_method_id);

            // Pickup = free; Door Delivery & Vendor Delivery = sum of vendor fees
            if (strtolower($shippingMethod->name) === 'pickup') {
                $shippingCost = 0;
            } else {
                // Sum delivery fees from all unique vendors in the cart
                $vendorIds = $cart->items->pluck('product.vendor_id')->unique();
                $shippingCost = Vendor::whereIn('id', $vendorIds)->sum('delivery_fee');
            }

            // Calculate coupon discount
            $discount = 0;
            $coupon = null;
            $couponCode = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();
                if ($coupon && $coupon->isValid() && (!$coupon->min_spend || $cart->total >= $coupon->min_spend)) {
                    $couponCode = $coupon->code;
                    if ($coupon->type === 'percentage') {
                        $discount = round($cart->total * ($coupon->value / 100), 2);
                    } else {
                        $discount = min($coupon->value, $cart->total);
                    }
                }
            }

            // Create order
            $order = Order::create([
                'order_number' => 'BN-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'shipping_method_id' => $shippingMethod->id,
                'subtotal' => $cart->total,
                'shipping_cost' => $shippingCost,
                'tax' => 0,
                'discount' => $discount,
                'coupon_code' => $couponCode,
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

            // Create order items and track vendors
            $vendorItems = [];
            foreach ($cart->items as $item) {
                $price = $item->product->sale_price ?? $item->product->price;
                
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

                // Group items by vendor
                $vendorId = $item->product->vendor_id;
                if (!isset($vendorItems[$vendorId])) {
                    $vendorItems[$vendorId] = [];
                }
                $vendorItems[$vendorId][] = $orderItem;

                // Decrease stock
                $item->product->decrement('quantity', $item->quantity);
            }

            // Record coupon usage
            if ($coupon && $couponCode) {
                DB::table('coupon_usages')->insert([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $coupon->increment('used_count');
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            // Send emails (outside transaction)
            try {
                // Send confirmation to customer
                $order->load('items.product', 'items.vendor');
                Mail::to(Auth::user()->email)->send(new OrderConfirmation($order));

                // Send notifications to each vendor
                foreach ($vendorItems as $vendorId => $items) {
                    $vendor = Vendor::with('user')->find($vendorId);
                    if ($vendor && $vendor->user && $vendor->user->email) {
                        Mail::to($vendor->user->email)->send(new NewOrderNotification($order, $vendor, $items));
                    }
                }
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Order email failed: ' . $e->getMessage());
            }

            return redirect()->route('checkout.confirmation', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order. Please try again.');
        }
    }

    /**
     * Order confirmation page.
     */
    public function confirmation($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        return view('shop.order-confirmation', compact('order'));
    }

    /**
     * Order history.
     */
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('shop.orders', compact('orders'));
    }

    /**
     * Order detail.
     */
    public function orderDetail($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.product', 'address')
            ->firstOrFail();

        return view('shop.order-detail', compact('order'));
    }
}
