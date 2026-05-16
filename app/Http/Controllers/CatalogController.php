<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: CatalogController (Fresh replacement for ShopController)
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the shop home page.
     */
    public function home()
    {
        $featuredCategories = Category::where('is_active', true)
            ->where('is_featured', true)
            ->take(6)
            ->get();

        $latestProducts = Product::active()
            ->latest()
            ->take(8)
            ->with(['category', 'images'])
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'images'])
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->with(['category', 'images'])
            ->take(8)
            ->get();

        $topStores = Vendor::approved()
            ->orderByDesc('rating')
            ->orderByDesc('total_sales')
            ->take(6)
            ->get();

        return view('shop.index', compact(
            'featuredCategories',
            'latestProducts',
            'featuredProducts',
            'bestSellers',
            'topStores'
        ));
    }

    /**
     * Display the product catalog.
     */
    public function index(Request $request)
    {
        $query = Product::active();

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('short_description', 'like', "%{$s}%")
                    ->orWhereHas('vendor', fn($vq) => $vq->where('store_name', 'like', "%{$s}%"));
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        // Price filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'avg_rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->with(['category', 'images', 'vendor'])->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('shop.catalog', compact('products', 'categories'));
    }

    /**
     * Display products by category.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = Product::active()
            ->where('category_id', $category->id)
            ->with(['category', 'images', 'vendor'])
            ->paginate(20);
            
        $categories = Category::where('is_active', true)->get();

        return view('shop.catalog', compact('products', 'categories', 'category'));
    }

    /**
     * Display product details.
     */
    public function product($slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'images', 'vendor', 'reviews.user'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
    }

    /**
     * Static pages and Contact.
     */
    public function about()
    {
        $stats = [
            'vendors' => Vendor::where('status', 'approved')->count(),
            'products' => Product::active()->count(),
            'orders' => Order::count(),
        ];
        return view('shop.about', compact('stats'));
    }

    public function contact()
    {
        return view('shop.contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:200',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function trackOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $order = Order::where('order_number', $request->order_number)->first();
            if (!$order) {
                return back()->with('error', 'Order not found. Please check the order number.');
            }
            return view('shop.track-order', compact('order'));
        }
        return view('shop.track-order');
    }
}
