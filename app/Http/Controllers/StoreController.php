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
