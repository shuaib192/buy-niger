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

        return view('shop.checkout', compact('cart', 'items', 'addresses', 'defaultAddress'));
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

            // Create order
            $order = Order::create([
                'order_number' => 'BN-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'subtotal' => $cart->total,
                'shipping_cost' => 0,
                'tax' => 0,
                'discount' => 0,
                'total' => $cart->total,
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

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            // Send emails (outside transaction)
            try {
                // Send confirmation to customer
                $order->load('items.product');
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
