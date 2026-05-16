<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: BasketController (Fresh replacement for CartController)
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $items = $cart ? $cart->items()->with('product.category', 'product.images', 'product.vendor')->get() : collect();
        
        return view('shop.cart', compact('cart', 'items'));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->quantity < $request->quantity) {
            $msg = 'Not enough stock available.';
            return $request->ajax() ? response()->json(['success' => false, 'message' => $msg]) : back()->with('error', $msg);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $product->current_price, // SAVE THE PRICE!
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Product added to cart!',
                'cart_count' => $cart->items->sum('quantity')
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $item = CartItem::with('product')->findOrFail($itemId);
        
        if ($item->product->quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Not enough stock.'], 422);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'item_total' => $item->quantity * $item->price,
            'cart_total' => $item->cart->total,
            'cart_count' => $item->cart->items->sum('quantity')
        ]);
    }

    public function count()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        return response()->json([
            'count' => $cart ? $cart->items->sum('quantity') : 0
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $item->delete();
        
        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }
        return back()->with('success', 'Cart cleared.');
    }
}
