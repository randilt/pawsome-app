<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        // Apply filters
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('min_price') || $request->has('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at', 'stock_quantity'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $products = $query->paginate($request->input('limit', 12));
        
        return response()->json($products);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return response()->json($product);
    }

    /**
     * Get reviews for a specific product
     */
    public function getProductReviews($productId)
    {
        try {
            // Validate product exists
            $product = Product::findOrFail($productId);
            
            // Get reviews from MongoDB
            $reviews = ProductReview::getProductReviews($productId, 50); // Get up to 50 reviews
            
            return response()->json([
                'success' => true,
                'data' => $reviews->toArray(),
                'message' => 'Reviews retrieved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching product reviews: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error fetching reviews'
            ], 500);
        }
    }

    /**
     * Get review statistics for a product
     */
    public function getReviewStats($productId)
    {
        try {
            // Validate product exists
            $product = Product::findOrFail($productId);
            
            // Get review statistics from MongoDB
            $averageRating = ProductReview::getProductAverageRating($productId);
            $ratingDistribution = ProductReview::getProductRatingDistribution($productId);
            $totalReviews = ProductReview::where('product_id', (int)$productId)->count();
            
            return response()->json([
                'success' => true,
                'average_rating' => $averageRating,
                'total_reviews' => $totalReviews,
                'rating_distribution' => $ratingDistribution,
                'message' => 'Review statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching review stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'average_rating' => 0.0,
                'total_reviews' => 0,
                'rating_distribution' => [],
                'message' => 'Error fetching review statistics'
            ], 500);
        }
    }

    /**
     * Submit a new review for a product
     */
    public function submitReview(Request $request, $productId)
    {
        try {
            // Validate product exists
            $product = Product::findOrFail($productId);
            
            // Validate request
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'required|string|max:255',
                'comment' => 'required|string|min:10',
                'image_url' => 'nullable|url',
            ]);

            // Check if user has purchased this product
            $hasPurchased = Auth::user()->orders()
                ->whereHas('orderItems', function($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->where('status', 'completed')
                ->exists();

            // Create review in MongoDB
            $review = ProductReview::create([
                'product_id' => (int)$productId,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'rating' => (int)$validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'verified_purchase' => $hasPurchased,
                'helpful_votes' => 0,
                'metadata' => [
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'user_email' => Auth::user()->email,
                    'image_url' => $validated['image_url'] ?? null,
                ]
            ]);

            // Log review event in analytics (if ProductAnalytics exists)
            try {
                if (class_exists('App\Models\ProductAnalytics')) {
                    \App\Models\ProductAnalytics::create([
                        'product_id' => (int)$productId,
                        'event_type' => 'review',
                        'user_id' => Auth::id(),
                        'session_id' => session()->getId(),
                        'metadata' => [
                            'rating' => (int)$validated['rating'],
                            'review_id' => $review->_id ?? uniqid()
                        ],
                        'timestamp' => now()
                    ]);
                }
            } catch (\Exception $e) {
                // Silent fail for analytics
                \Log::warning('Failed to log review analytics: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'review' => [
                    'id' => $review->_id ?? $review->id,
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'comment' => $review->comment,
                    'verified_purchase' => $review->verified_purchase,
                    'created_at' => $review->created_at,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error submitting review: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting review: ' . $e->getMessage()
            ], 500);
        }
    }
}