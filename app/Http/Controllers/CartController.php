<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: CartController
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get or create cart for current user/session.
     */
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart = $this->getCart();
        $items = $cart->items()->with('product.category')->get();
        
        // Auto-cleanup items where the product was deleted
        $hasOrphans = false;
        foreach ($items as $item) {
            if (!$item->product) {
                $item->delete();
                $hasOrphans = true;
            }
        }
        
        if ($hasOrphans) {
            $items = $cart->items()->with('product.category')->get();
            $cart->load('items');
        }
        
        return view('shop.cart', compact('cart', 'items'));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        $variantId = $request->product_variant_id; // Added variant support

        // Check stock
        if ($product->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        $cart = $this->getCart();
        
        // Check if item already in cart (now including variant check)
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();
        
        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            // Use the variant price if available, fallback to product's current_price
            // round() prevents floating point bugs (e.g. 1199.97 instead of 1200.00)
            $price = round((float) $product->current_price, 2);
            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if ($variant && $variant->price > 0) {
                    $price = round((float) $variant->price, 2);
                }
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to cart!',
            'cart_count' => $cart->fresh()->item_count
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);
        
        // Check stock
        if ($item->product->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock'
            ], 400);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'item_total' => round((float)$item->price * (int)$request->quantity),
            'cart_total' => round((float)$cart->fresh()->total),
            'cart_count' => $cart->fresh()->item_count
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove($itemId)
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from cart',
            'cart_total' => round((float)$cart->fresh()->total),
            'cart_count' => $cart->fresh()->item_count
        ]);
    }

    /**
     * Get cart count for AJAX.
     */
    public function count()
    {
        $cart = $this->getCart();
        
        return response()->json([
            'count' => $cart->item_count
        ]);
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared');
    }
}
