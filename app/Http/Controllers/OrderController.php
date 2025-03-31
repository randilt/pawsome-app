<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }
        
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'You are not authorized to view this order.');
        }
        
        return view('orders.show', compact('order'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login to place an order.'], 401);
        }
        
        try {
            DB::beginTransaction();
            
            // Calculate total
            $total = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'error' => "Not enough stock available for {$product->name}."
                    ], 400);
                }
                
                $total += $product->price * $item['quantity'];
            }
            
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'shipping_address' => $validated['shipping_address'],
                'status' => 'pending',
            ]);
            
            // Create order items and update stock
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_time' => $product->price,
                ]);
                
                // Update product stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
    }
}

