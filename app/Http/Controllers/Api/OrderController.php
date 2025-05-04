<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            
            $totalAmount = 0;
            $orderItems = [];
            
            // Validate and process items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                
                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;
                
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $product->price,
                ];
                
                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }
            
            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
            ]);
            
            // Create order items
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order' => $order->load('orderItems.product'),
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::with(['orderItems.product'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with(['orderItems.product'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
            
        return response()->json($order);
    }
}

