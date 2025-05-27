<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAnalytics;
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

                // Log purchase to MongoDB analytics
                try {
                    ProductAnalytics::logPurchase(
                        $product->id,
                        Auth::id(),
                        $order->id,
                        $item['quantity'],
                        $product->price
                    );
                } catch (\Exception $e) {
                    // Silent fail for analytics
                }
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

    /**
     * Display a listing of orders for admin.
     */
    public function adminIndex()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get order analytics from MongoDB
        try {
            $analytics = [
                'recent_purchases' => ProductAnalytics::where('event_type', 'purchase')
                    ->where('timestamp', '>=', now()->subDays(7))
                    ->count(),
                'top_selling_products' => $this->getTopSellingProducts(),
                'conversion_stats' => $this->getConversionStats()
            ];
        } catch (\Exception $e) {
            $analytics = [
                'recent_purchases' => 0,
                'top_selling_products' => [],
                'conversion_stats' => []
            ];
        }
        
        return view('admin.orders.index', compact('orders', 'analytics'));
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);
        
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $validated['status'],
        ]);
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Get top selling products from MongoDB analytics
     */
    private function getTopSellingProducts($days = 30, $limit = 5)
    {
        try {
            $pipeline = [
                ['$match' => [
                    'event_type' => 'purchase',
                    'timestamp' => ['$gte' => now()->subDays($days)]
                ]],
                ['$group' => [
                    '_id' => '$product_id',
                    'total_sold' => ['$sum' => '$metadata.quantity'],
                    'total_revenue' => ['$sum' => ['$multiply' => ['$metadata.quantity', '$metadata.price']]]
                ]],
                ['$sort' => ['total_sold' => -1]],
                ['$limit' => $limit]
            ];

            return ProductAnalytics::raw(function($collection) use ($pipeline) {
                return $collection->aggregate($pipeline);
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get conversion statistics from MongoDB
     */
    private function getConversionStats($days = 30)
    {
        try {
            $pipeline = [
                ['$match' => [
                    'timestamp' => ['$gte' => now()->subDays($days)]
                ]],
                ['$group' => [
                    '_id' => '$event_type',
                    'count' => ['$sum' => 1]
                ]]
            ];

            $results = ProductAnalytics::raw(function($collection) use ($pipeline) {
                return $collection->aggregate($pipeline);
            });

            $stats = [];
            foreach ($results as $result) {
                $stats[$result['_id']] = $result['count'];
            }

            // Calculate conversion rates
            $views = $stats['view'] ?? 0;
            $cartAdds = $stats['cart_add'] ?? 0;
            $purchases = $stats['purchase'] ?? 0;

            return [
                'views' => $views,
                'cart_adds' => $cartAdds,
                'purchases' => $purchases,
                'view_to_cart_rate' => $views > 0 ? round(($cartAdds / $views) * 100, 2) : 0,
                'cart_to_purchase_rate' => $cartAdds > 0 ? round(($purchases / $cartAdds) * 100, 2) : 0,
                'overall_conversion_rate' => $views > 0 ? round(($purchases / $views) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}