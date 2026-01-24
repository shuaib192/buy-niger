<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: WishlistController
 */

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display current user's wishlist.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with('product.category')
            ->latest()
            ->paginate(12);

        return view('shop.wishlist', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist (AJAX).
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add to wishlist',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Already in wishlist'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist!'
        ]);
    }

    /**
     * Remove product from wishlist.
     */
    public function remove($productId)
    {
        if (!Auth::check()) {
             return redirect()->route('login');
        }

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();
            
        // If AJAX request
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Removed from wishlist']);
        }

        return back()->with('success', 'Removed from wishlist');
    }
    /**
     * Move product from wishlist to cart.
     */
    public function moveToCart($productId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login'], 401);
        }

        // 1. Verify wishlist item exists
        $wishlistItem = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json(['success' => false, 'message' => 'Item not in wishlist']);
        }

        // 2. Add to Cart
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $product = Product::find($productId);
        if (!$product) {
             return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        if ($product->quantity < 1) {
            return response()->json(['success' => false, 'message' => 'Product out of stock']);
        }

        $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => $product->sale_price ?? $product->price
            ]);
        }
        
        // Update Cart Total
        $newTotal = $cart->items()->selectRaw('sum(quantity * price) as total')->value('total');
        $cart->update(['total' => $newTotal]);

        // 3. Remove from Wishlist
        $wishlistItem->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Moved to cart successfully',
            'cart_count' => $cart->items()->sum('quantity')
        ]);
    }
}
