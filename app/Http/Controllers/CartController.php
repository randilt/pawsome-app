<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // For AJAX requests, return cart count
            $cart = Session::get('cart', []);
            $count = array_sum(array_column($cart, 'quantity'));
            
            return response()->json([
                'count' => $count,
                'cart' => $cart
            ]);
        }
        
        return view('cart.index');
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $quantity = $request->input('quantity', 1);
        
        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.'
            ], 400);
        }
        
        $cart = Session::get('cart', []);
        
        // Check if product already exists in cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image_url' => $product->image_url
            ];
        }
        
        Session::put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $quantity = $request->input('quantity');
        
        if ($quantity <= 0) {
            return $this->remove($request, $itemId);
        }
        
        $cart = Session::get('cart', []);
        
        if (!isset($cart[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.'
            ], 404);
        }
        
        // Check product stock
        $product = Product::find($itemId);
        if ($product && $product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.'
            ], 400);
        }
        
        $cart[$itemId]['quantity'] = $quantity;
        Session::put('cart', $cart);
        
        // Calculate subtotal and total
        $subtotal = $cart[$itemId]['price'] * $quantity;
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated!',
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
            'count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Remove item from cart.  => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request, $itemId)
    {
        $cart = Session::get('cart', []);
        
        if (!isset($cart[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.'
            ], 404);
        }
        
        unset($cart[$itemId]);
        Session::put('cart', $cart);
        
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'total' => number_format($total, 2),
            'count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
        // Don't redirect if using localStorage cart
        // Just render the checkout view
        return view('cart.checkout');
    }

    /**
     * Process the checkout and create an order.
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|string',
        ]);

        // The actual order processing is handled by JavaScript
        // which calls the OrderController's store method via API
        
        return redirect()->route('orders.index')
            ->with('success', 'Order placed successfully!');
    }
}

