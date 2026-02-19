<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: StoreController - Public Vendor Storefront
 */

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * List all vendor stores.
     */
    public function index(Request $request)
    {
        $query = Vendor::where('status', 'approved')
            ->with('user');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('store_description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $stores = $query->latest()->paginate(12);

        return view('shop.stores', compact('stores'));
    }

    /**
     * Display vendor storefront.
     */
    public function show($slug)
    {
        $vendor = Vendor::where('store_slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $products = Product::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->with('category', 'images')
            ->latest()
            ->paginate(12);

        $categories = $products->pluck('category')->unique('id')->filter();

        return view('shop.store', compact('vendor', 'products', 'categories'));
    }
}
