<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}

