<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: VendorController
 */

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\VendorBankDetail;
use App\Models\VendorPayout;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Display vendor dashboard with stats.
     */
    public function dashboard(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) return redirect()->route('home')->with('error', 'Vendor profile not found.');

        // Get real order stats
        $orderItems = OrderItem::where('vendor_id', $vendor->id);
        
        $stats = [
            'total_products' => Product::where('vendor_id', $vendor->id)->count(),
            'active_products' => Product::where('vendor_id', $vendor->id)->where('status', 'active')->count(),
            'total_orders' => $orderItems->clone()->count(),
            'pending_orders' => $orderItems->clone()->whereIn('status', ['pending', 'processing'])->count(),
            'paid_orders' => $orderItems->clone()->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))->count(),
            'pending_earnings' => $orderItems->clone()->whereIn('status', ['processing', 'shipped'])->sum('subtotal'),
            'total_earnings' => $orderItems->clone()->where('status', 'delivered')->sum('subtotal'),
            'store_rating' => $vendor->rating ?? 0,
        ];

        // Get recent orders
        $recentOrders = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        // Chart Data Filtering
        $period = $request->get('period', 'monthly');
        $chartData = $this->getChartData($vendor->id, $period);

        // Top Products by sales count
        $topProducts = OrderItem::where('vendor_id', $vendor->id)
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Get low stock products
        $lowStockProducts = Product::where('vendor_id', $vendor->id)
            ->where('quantity', '<=', 5)
            ->where('status', 'active')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact('vendor', 'stats', 'recentOrders', 'chartData', 'lowStockProducts', 'topProducts', 'period'));
    }

    /**
     * Helper to get sales chart data based on period.
     */
    private function getChartData($vendorId, $period)
    {
        $data = [];
        $labels = [];
        $query = OrderItem::where('vendor_id', $vendorId)->where('status', 'delivered');

        if ($period === 'daily') {
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = now()->subDays($i)->format('d M');
                $data[] = $query->clone()->whereDate('created_at', $date)->sum('subtotal');
            }
        } elseif ($period === 'weekly') {
            for ($i = 11; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end = now()->subWeeks($i)->endOfWeek();
                $labels[] = 'Week ' . $start->format('W');
                $data[] = $query->clone()->whereBetween('created_at', [$start, $end])->sum('subtotal');
            }
        } else { // monthly
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $data[] = $query->clone()->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('subtotal');
            }
        }

        return ['labels' => $labels, 'datasets' => [['label' => 'Sales (₦)', 'data' => $data]]];
    }

    /**
     * List all orders for vendor.
     */
    public function orders(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        $query = OrderItem::where('vendor_id', $vendor->id)
            ->with('order.user', 'product');

        // Status counts for tabs
        $counts = [
            'all' => OrderItem::where('vendor_id', $vendor->id)->count(),
            'pending' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'processing' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'processing')->count(),
            'shipped' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'shipped')->count(),
            'delivered' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'delivered')->count(),
            'cancelled' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'cancelled')->count(),
        ];

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status != 'all') {
            $query->where('status', $status);
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('vendor.orders', compact('orderItems', 'counts', 'status'));
    }

    /**
     * View single order item.
     */
    public function orderDetail($id)
    {
        $vendor = Auth::user()->vendor;
        
        $orderItem = OrderItem::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->with('order.user', 'order.address', 'product')
            ->firstOrFail();

        return view('vendor.order-detail', compact('orderItem'));
    }

    /**
     * Display inventory management screen.
     */
    public function inventory(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $query = Product::where('vendor_id', $vendor->id)->with(['variants', 'category']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15)->withQueryString();
        
        // Get recent stock history
        $history = StockHistory::whereHas('product', fn($q) => $q->where('vendor_id', $vendor->id))
            ->with(['product', 'variant'])
            ->latest()
            ->take(10)
            ->get();

        return view('vendor.inventory', compact('products', 'history'));
    }

    /**
     * Display vendor analytics dashboard.
     */
    public function analytics()
    {
        $vendor = Auth::user()->vendor;
        
        // Simple analytics data
        $totalRevenue = OrderItem::where('vendor_id', $vendor->id)
            ->where('status', 'delivered')
            ->sum('subtotal');
            
        $totalOrders = OrderItem::where('vendor_id', $vendor->id)->count();
        $totalProducts = Product::where('vendor_id', $vendor->id)->count();
        
        // Sales over time (last 30 days)
        $salesData = OrderItem::where('vendor_id', $vendor->id)
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(subtotal) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('vendor.analytics', compact('vendor', 'totalRevenue', 'totalOrders', 'totalProducts', 'salesData'));
    }

    /**
     * Update stock level via AJAX or form.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'change' => 'required|integer',
            'type' => 'required|string|in:restock,adjustment,return',
            'reason' => 'nullable|string|max:255',
        ]);

        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($request->product_id);

        if ($request->variant_id) {
            $variant = $product->variants()->findOrFail($request->variant_id);
            $oldStock = $variant->stock_quantity;
            $newStock = $oldStock + $request->change;
            $variant->update(['stock_quantity' => $newStock]);
            
            // Log history
            StockHistory::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'change_amount' => $request->change,
                'new_stock_level' => $newStock,
                'type' => $request->type,
                'reason' => $request->reason,
                'user_id' => Auth::id()
            ]);
        } else {
            $oldStock = $product->quantity;
            $newStock = $oldStock + $request->change;
            $product->update(['quantity' => $newStock]);
            
            // Log history
            StockHistory::create([
                'product_id' => $product->id,
                'change_amount' => $request->change,
                'new_stock_level' => $newStock,
                'type' => $request->type,
                'reason' => $request->reason,
                'user_id' => Auth::id()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'new_stock' => $newStock]);
        }

        return back()->with('success', 'Stock updated successfully!');
    }

    /**
     * Update order item status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $vendor = Auth::user()->vendor;
        
        $orderItem = OrderItem::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        $orderItem->update(['status' => $request->status]);

        // If shipped, add tracking number
        if ($request->status == 'shipped' && $request->tracking_number) {
            $orderItem->update(['tracking_number' => $request->tracking_number]);
        }

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }

    /**
     * Export orders to CSV.
     */
    public function exportOrders()
    {
        $vendor = Auth::user()->vendor;
        $orderItems = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->get();

        $filename = "orders_export_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');
        
        // Headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        
        fputcsv($handle, ['Order ID', 'Product', 'Customer', 'Quantity', 'Price', 'Status', 'Date']);
        
        foreach ($orderItems as $item) {
            fputcsv($handle, [
                $item->order->order_number,
                $item->product->name,
                $item->order->user->name,
                $item->quantity,
                $item->price,
                $item->status,
                $item->created_at->format('Y-m-d H:i')
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Display vendor store settings.
     */
    public function settings()
    {
        $vendor = Auth::user()->vendor;
        return view('vendor.settings', compact('vendor'));
    }

    /**
     * Update vendor store settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
            'bank_name' => 'nullable|string|max:100',
            'account_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
        ]);

        $vendor = Auth::user()->vendor;

        $data = $request->only([
            'store_name', 'description', 'address', 'city', 'state',
            'meta_title', 'meta_description', 'facebook', 'twitter', 'instagram'
        ]);

        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $data['logo'] = $request->file('logo')->store('vendors/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($vendor->banner) {
                Storage::disk('public')->delete($vendor->banner);
            }
            $data['banner'] = $request->file('banner')->store('vendors/banners', 'public');
        }

        $vendor->update($data);

        // Update or create primary bank detail
        if ($request->bank_name && $request->account_number) {
            VendorBankDetail::updateOrCreate(
                ['vendor_id' => $vendor->id, 'is_primary' => true],
                [
                    'bank_name' => $request->bank_name,
                    'account_name' => $request->account_name,
                    'account_number' => $request->account_number,
                ]
            );
        }

        return back()->with('success', 'Store settings updated successfully!');
    }
    /**
     * Display vendor finance dashboard.
     */
    public function finances()
    {
        $vendor = Auth::user()->vendor;
        $orderItems = OrderItem::where('vendor_id', $vendor->id);
        
        $payouts = VendorPayout::where('vendor_id', $vendor->id)->latest()->paginate(10);
        $bankDetails = VendorBankDetail::where('vendor_id', $vendor->id)->get();

        $stats = [
            'total_earned' => $orderItems->clone()->where('status', 'delivered')->sum('subtotal'),
            'pending_payout' => $orderItems->clone()->whereIn('status', ['processing', 'shipped'])->sum('subtotal'),
            'available_balance' => $vendor->balance,
        ];

        return view('vendor.finances', compact('vendor', 'payouts', 'bankDetails', 'stats'));
    }

    /**
     * Handle payout request.
     */
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_detail_id' => 'required|exists:vendor_bank_details,id'
        ]);

        $vendor = Auth::user()->vendor;

        if ($request->amount > $vendor->balance) {
            return back()->with('error', 'Insufficient balance for this payout request.');
        }

        // Create payout record
        VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $request->amount,
            'reference' => 'PAY-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'payment_details' => [
                'bank_detail_id' => $request->bank_detail_id
            ]
        ]);

        // Deduct from balance
        $vendor->decrement('balance', $request->amount);

    }

    /**
     * Display vendor profile (personal settings).
     */
    public function profile()
    {
        $user = Auth::user();
        return view('vendor.profile', compact('user'));
    }

    /**
     * Update vendor profile (personal settings).
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
     * Bulk actions for products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $vendor = Auth::user()->vendor;
        $products = Product::where('vendor_id', $vendor->id)->whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            if ($request->action === 'delete') {
                foreach ($product->images as $image) {
                     Storage::disk('public')->delete($image->image_path);
                }
                $product->delete();
            } elseif ($request->action === 'activate') {
                $product->update(['status' => 'active']);
            } elseif ($request->action === 'deactivate') {
                $product->update(['status' => 'inactive']);
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    /**
     * List vendor products.
     */
    public function products(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::where('vendor_id', $vendor->id)->with(['category', 'images']);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Status
        if ($request->filled('status')) {
            if ($request->status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'stock_low':
                $query->orderBy('quantity', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(15)->withQueryString();
            
        return view('vendor.products.index', compact('products', 'categories', 'sort'));
    }

    /**
     * Show create product form.
     */
    public function createProduct()
    {
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store new product.
     */
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'tags' => 'nullable|string',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        $vendor = Auth::user()->vendor;

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . Str::random(6)),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->compare_price ?? null,
            'quantity' => $request->quantity,
            'sku' => $request->sku ?? Str::upper(Str::random(8)),
            'status' => 'active',
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index
                ]);
            }
        }

        // Handle Variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'price' => !empty($variant['price']) ? $variant['price'] : null,
                        'stock_quantity' => $variant['stock'] ?? 0,
                        'sku' => !empty($variant['sku']) ? $variant['sku'] : $product->sku . '-' . Str::upper(Str::random(4))
                    ]);
                }
            }
        }

        // Handle Tags
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            foreach ($tags as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
                    $product->tags()->attach($tag->id);
                }
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product published successfully!');
    }

    /**
     * Show edit product form.
     */
    public function editProduct($id)
    {
        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->get();
        
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product.
     */
    public function updateProduct(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->compare_price ?? null,
            'quantity' => $request->quantity,
            'sku' => $request->sku ?? $product->sku,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle New Images
        if ($request->hasFile('images')) {
            $nextOrder = $product->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $nextOrder + $index
                ]);
            }
        }

        // Handle Reordering and Primary Image
        if ($request->has('image_order')) {
            foreach ($request->image_order as $index => $imageId) {
                $product->images()->where('id', $imageId)->update([
                    'sort_order' => $index,
                    'is_primary' => $index === 0
                ]);
            }
        }
        
        // Handle Image Deletion
        if ($request->delete_images) {
            foreach ($request->delete_images as $imgId) {
                $img = $product->images()->find($imgId);
                if ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
        }

        // Handle Variants
        // For simplicity, we'll replace variants if they are sent. 
        // A more complex system would track IDs, but usually vendors re-add.
        if ($request->has('variants')) {
            $product->variants()->delete();
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'price' => !empty($variant['price']) ? $variant['price'] : null,
                        'stock_quantity' => $variant['stock'] ?? 0,
                        'sku' => !empty($variant['sku']) ? $variant['sku'] : $product->sku . '-' . Str::upper(Str::random(4))
                    ]);
                }
            }
        }
        
        // Handle Tags
        if ($request->tags) {
            $product->tags()->detach();
            $tags = array_map('trim', explode(',', $request->tags));
            foreach ($tags as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
                    $product->tags()->attach($tag->id);
                }
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product.
     */
    public function destroyProduct($id)
    {
        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        
        // Delete images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->images()->delete();
        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

    /**
     * List vendor coupons.
     */
    public function coupons()
    {
        $vendor = Auth::user()->vendor;
        $coupons = \App\Models\Coupon::where('vendor_id', $vendor->id)->latest()->get();
        return view('vendor.coupons.index', compact('coupons'));
    }

    /**
     * Store new coupon.
     */
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:20',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $vendor = Auth::user()->vendor;

        \App\Models\Coupon::create([
            'vendor_id' => $vendor->id,
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_active' => true,
        ]);

        return back()->with('success', 'Coupon created successfully!');
    }

    /**
     * Delete coupon.
     */
    public function destroyCoupon($id)
    {
        $vendor = Auth::user()->vendor;
        $coupon = \App\Models\Coupon::where('vendor_id', $vendor->id)->findOrFail($id);
        $coupon->delete();

        return back()->with('success', 'Coupon deleted successfully!');
    }

    /**
     * Toggle coupon active status.
     */
    public function toggleCouponStatus(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $coupon = \App\Models\Coupon::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $coupon->is_active = $request->active ? true : false;
        $coupon->save();

        return response()->json(['success' => true]);
    }
}
