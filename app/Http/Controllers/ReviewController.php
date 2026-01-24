<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: ReviewController
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        // Check if user has already reviewed this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Optional: Check if user has purchased the product
        $hasPurchased = Order::where('user_id', Auth::id())
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->exists();

        if (!$hasPurchased) {
             // We can choose to allow reviews even without purchase, but it's recommended to check.
             // For now, let's just log it or allow it but maybe mark it as "Verified Purchase" in the future.
             // return back()->with('error', 'You can only review products you have purchased and received.');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve for now, can add moderation later
        ]);

        // Update product rating
        $this->updateProductRating($product);

        return back()->with('success', 'Thank you for your review!');
    }

    /**
     * Display a listing of user's reviews.
     */
    public function index()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with('product')
            ->latest()
            ->paginate(10);
            
        return view('customer.reviews.index', compact('reviews'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        $this->updateProductRating($review->product);

        return back()->with('success', 'Review updated successfully');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id() && Auth::user()->role_id !== 1) {
            abort(403);
        }

        $product = $review->product;
        $review->delete();

        // Update product rating after deletion
        $this->updateProductRating($product);

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Update the product average rating and rating count.
     */
    private function updateProductRating(Product $product)
    {
        $reviews = $product->reviews()->where('is_approved', true);
        $count = $reviews->count();
        $average = $count > 0 ? $reviews->avg('rating') : 0;

        $product->update([
            'rating' => $average,
            'rating_count' => $count,
        ]);
    }
}
