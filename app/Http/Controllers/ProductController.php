<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductAnalytics;
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
        
        // Get categories for filter
        $categories = Category::all();
        
        // Get price stats
        $stats = [
            'min_price' => Product::min('price'),
            'max_price' => Product::max('price'),
            'avg_price' => Product::avg('price'),
            'total_products' => Product::count(),
            'in_stock_count' => Product::where('stock_quantity', '>', 0)->count(),
        ];

        // Get popular products from MongoDB analytics
        try {
            $popularProducts = ProductAnalytics::getPopularProducts(30, 5);
            $stats['popular_products'] = $popularProducts;
        } catch (\Exception $e) {
            $stats['popular_products'] = [];
        }

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Log product view in MongoDB
        try {
            ProductAnalytics::logView($id, Auth::id(), [
                'product_name' => $product->name,
                'category' => $product->category->name
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log product view: ' . $e->getMessage());
        }

        // Get reviews from MongoDB
        $reviews = collect();
        $averageRating = 0;
        $ratingDistribution = [];
        
        try {
            // Get reviews for this product
            $reviews = ProductReview::getProductReviews($id, 20);
            
            // Get average rating
            $averageRating = ProductReview::getProductAverageRating($id);
            
            // Get rating distribution
            $ratingDistribution = ProductReview::getProductRatingDistribution($id);
            
        } catch (\Exception $e) {
            \Log::error('MongoDB review fetch error: ' . $e->getMessage());
        }
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();
        
        return view('products.show', compact(
            'product', 
            'reviews', 
            'averageRating', 
            'ratingDistribution',
            'relatedProducts'
        ));
    }

    /**
     * Store a product review (MongoDB)
     */
    public function storeReview(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10',
        ]);

        $product = Product::findOrFail($productId);
        
        // Check if user has purchased this product
        $hasPurchased = Auth::user()->orders()
            ->whereHas('orderItems', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->where('status', 'completed')
            ->exists();

        try {
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
                    'user_email' => Auth::user()->email
                ]
            ]);

            // Log review event in analytics
            ProductAnalytics::create([
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

            return response()->json([
                'success' => true,
                'message' => 'Review added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the admin product management page with analytics.
     */
    public function adminIndex()
    {
        $products = Product::with('category')->paginate(15);
        $categories = Category::all();
        
        // Get analytics data from MongoDB
        try {
            $totalViews = ProductAnalytics::where('event_type', 'view')
                ->where('timestamp', '>=', now()->subDays(30))
                ->count();
                
            $totalCartAdds = ProductAnalytics::where('event_type', 'cart_add')
                ->where('timestamp', '>=', now()->subDays(30))
                ->count();
                
            $popularProducts = ProductAnalytics::getPopularProducts(30, 10);
            
            $analytics = [
                'total_views' => $totalViews,
                'total_cart_adds' => $totalCartAdds,
                'popular_products' => $popularProducts,
            ];
        } catch (\Exception $e) {
            \Log::error('MongoDB analytics error in adminIndex: ' . $e->getMessage());
            $analytics = [
                'total_views' => 0,
                'total_cart_adds' => 0,
                'popular_products' => [],
            ];
        }
        
        return view('admin.products.index', compact('products', 'categories', 'analytics'));
    }

    /**
     * Show analytics for a specific product
     */
    public function showAnalytics($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        try {
            // Get analytics data from MongoDB
            $analytics = [
                'views_last_30_days' => ProductAnalytics::where('product_id', (int)$id)
                    ->where('event_type', 'view')
                    ->where('timestamp', '>=', now()->subDays(30))
                    ->count(),
                'cart_adds_last_30_days' => ProductAnalytics::where('product_id', (int)$id)
                    ->where('event_type', 'cart_add')
                    ->where('timestamp', '>=', now()->subDays(30))
                    ->count(),
                'purchases_last_30_days' => ProductAnalytics::where('product_id', (int)$id)
                    ->where('event_type', 'purchase')
                    ->where('timestamp', '>=', now()->subDays(30))
                    ->count(),
                'conversion_funnel' => ProductAnalytics::getConversionFunnel($id, 30),
                'average_rating' => ProductReview::getProductAverageRating($id),
                'total_reviews' => ProductReview::where('product_id', (int)$id)->count(),
                'rating_distribution' => ProductReview::getProductRatingDistribution($id),
                // Add the missing keys that the analytics view expects
                'total_views' => ProductAnalytics::where('product_id', (int)$id)
                    ->where('event_type', 'view')
                    ->where('timestamp', '>=', now()->subDays(30))
                    ->count(),
                'total_cart_adds' => ProductAnalytics::where('product_id', (int)$id)
                    ->where('event_type', 'cart_add')
                    ->where('timestamp', '>=', now()->subDays(30))
                    ->count(),
            ];
        } catch (\Exception $e) {
            $analytics = [
                'views_last_30_days' => 0,
                'cart_adds_last_30_days' => 0,
                'purchases_last_30_days' => 0,
                'conversion_funnel' => [],
                'average_rating' => 0,
                'total_reviews' => 0,
                'rating_distribution' => [],
                'total_views' => 0,
                'total_cart_adds' => 0,
            ];
        }
        
        return view('admin.products.analytics', compact('product', 'analytics'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products',
            'description' => 'required|string|min:10',
            'long_description' => 'required|string|min:50',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'variants' => 'nullable|json',
            'status' => 'nullable|in:active,inactive',
        ]);

        $product = Product::create($validated);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'description' => 'required|string|min:10',
            'long_description' => 'required|string|min:50',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'variants' => 'nullable|json',
            'status' => 'nullable|in:active,inactive',
        ]);

        $product->update($validated);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->orderItems()->count() > 0) {
            $product->update(['status' => 'inactive']);
            return redirect()->route('admin.products.index')->with('success', 'Product marked as inactive due to existing orders.');
        }
        
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Display a listing of products for API
     */
    public function apiIndex(Request $request)
    {
        $query = Product::with('category')->active();

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('min_price') || $request->has('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at', 'stock_quantity'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate($request->input('limit', 12));

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * Display the specified product for API
     */
    public function apiShow($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ]);
    }
}