<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: ShopController
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $featuredCategories = Category::where('is_active', true)
            ->where('is_featured', true)
            ->take(6)
            ->get();

        $latestProducts = Product::where('status', 'active')
            ->latest()
            ->take(8)
            ->with('category', 'images')
            ->get();

        $featuredProducts = Product::where('status', 'active')
            ->where('is_featured', true)
            ->with('category', 'images')
            ->take(8)
            ->get();

        return view('shop.index', compact('featuredCategories', 'latestProducts', 'featuredProducts'));
    }

    /**
     * Display the product catalog.
     */
    public function catalog(Request $request)
    {
        $query = Product::where('status', 'active');

        // Search (Name, Description, Vendor Name)
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%")
                  ->orWhereHas('vendor', function($vq) use ($s) {
                      $vq->where('store_name', 'like', "%{$s}%");
                  });
            });
        }

        // Category
        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Price filter
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->rating) {
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
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->with(['category', 'images', 'vendor'])->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('shop.catalog', compact('products', 'categories'));
    }

    /**
     * Display a single product.
     */
    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with('category', 'images', 'vendor', 'reviews')
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
    }

    /**
     * Get search suggestions for autocomplete.
     */
    public function suggestions(Request $request)
    {
        $s = $request->search;
        if (strlen($s) < 2) return response()->json([]);

        $products = \App\Models\Product::where('status', 'active')
            ->where('name', 'like', "%{$s}%")
            ->take(5)
            ->get(['id', 'name', 'slug']);

        $categories = \App\Models\Category::where('is_active', true)
            ->where('name', 'like', "%{$s}%")
            ->take(3)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * About page.
     */
    public function about()
    {
        $vendorCount = \App\Models\Vendor::where('status', 'approved')->count();
        $productCount = \App\Models\Product::where('status', 'active')->count();
        $orderCount = \App\Models\Order::count();
        return view('shop.about', compact('vendorCount', 'productCount', 'orderCount'));
    }

    /**
     * Contact page.
     */
    public function contact()
    {
        return view('shop.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:200',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        // Save to database
        ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

        // Send email to admin
        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Name: {$request->name}\nEmail: {$request->email}\nSubject: {$request->subject}\n\nMessage:\n{$request->message}",
                function ($mail) use ($request) {
                    $mail->to(config('mail.from.address'))
                         ->subject('BuyNiger Contact: ' . $request->subject)
                         ->replyTo($request->email, $request->name);
                }
            );
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent! We\'ll get back to you shortly.');
    }

    /**
     * Public Order Tracking.
     */
    public function trackOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'order_number' => 'required|string',
            ]);

            $search = $request->order_number;

            $order = \App\Models\Order::where('order_number', $search)
                ->orWhere('shipping_address->tracking_id', $search)
                ->with('items.product')
                ->first();

            if (!$order) {
                return back()->with('error', 'Order not found. Please check your details and try again.');
            }

            return view('shop.track-order', compact('order'));
        }

        return view('shop.track-order');
    }
}

