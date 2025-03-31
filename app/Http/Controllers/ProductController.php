<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return view('products.show', compact('product'));
    }

    /**
     * Display the admin product management page.
     */
    public function adminIndex()
    {
        $products = Product::with('category')->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
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

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
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

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product is in any orders
        if ($product->orderItems()->count() > 0) {
            // Soft delete by marking as inactive
            $product->update(['status' => 'inactive']);
            return redirect()->route('admin.products.index')
                ->with('success', 'Product marked as inactive due to existing orders.');
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

        /**
     * Display a listing of products for API
     */
    public function apiIndex(Request $request)
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

