<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('orders.index', compact('orders'));
    }

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
                if ($product->stock_quantity< $item['quantity']) {
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
                $order->items()->create($item);
            }
            
            DB::commit();
            
            return redirect()->route('orders.index')
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
            
        return view('orders.show', compact('order'));
    }

    /**
     * Display a listing of all orders (admin).
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items.product']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->status($request->status);
        }
        
        // Sort orders
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $orders = $query->paginate(15);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Update the status of the specified order (admin).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        
        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order status updated successfully.');
    }
}

