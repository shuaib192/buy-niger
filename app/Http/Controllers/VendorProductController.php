<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: VendorProductController
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    /**
     * Display vendor's product list.
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $query = Product::where('vendor_id', $vendor->id);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status === 'active') {
            $query->where('status', 'active');
        } elseif ($request->status === 'inactive') {
            $query->whereIn('status', ['inactive', 'draft']);
        }

        $products = $query->with('category', 'images')->latest()->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('vendor.products.index', compact('products', 'categories'));
    }

    /**
     * Show create product form.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $vendor = Auth::user()->vendor;

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'description' => $request->description,
            'short_description' => Str::limit(strip_tags($request->description), 200),
            'price' => $request->price,
            'sale_price' => $request->compare_price,
            'sku' => $request->sku ?? 'BN-' . strtoupper(Str::random(8)),
            'quantity' => $request->quantity,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product created successfully!');
    }

    /**
     * Show edit product form.
     */
    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $categories = Category::where('is_active', true)->get();
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update a product.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'status' => 'in:active,inactive,draft',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => Str::limit(strip_tags($request->description), 200),
            'price' => $request->price,
            'sale_price' => $request->compare_price,
            'sku' => $request->sku,
            'quantity' => $request->quantity,
            'status' => $request->status ?? $product->status,
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $maxSort = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $maxSort + $index + 1,
                    'is_primary' => $product->images()->count() === 0 && $index === 0,
                ]);
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product active status.
     */
    public function toggleStatus(Product $product)
    {
        $this->authorizeProduct($product);
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);
        return back()->with('success', 'Product status updated!');
    }

    /**
     * Ensure the product belongs to the current vendor.
     */
    private function authorizeProduct(Product $product)
    {
        if ($product->vendor_id !== Auth::user()->vendor->id) {
            abort(403, 'Unauthorized');
        }
    }
}
