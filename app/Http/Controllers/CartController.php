<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();
        $cartCount = $this->cartService->getCartCount();

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    public function add(Request $request, $productId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'options' => 'array'
            ]);

            $cartItem = $this->cartService->addToCart(
                $productId,
                $request->quantity,
                $request->options ?? []
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart',
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal()
                ]);
            }

            return redirect()->back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $cartItemId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:0'
            ]);

            $cartItem = $this->cartService->updateCartItem($cartItemId, $request->quantity);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal(),
                    'item_subtotal' => $cartItem->subtotal
                ]);
            }

            return redirect()->back()->with('success', 'Cart updated!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $request, $cartItemId)
    {
        try {
            $this->cartService->removeFromCart($cartItemId);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal()
                ]);
            }

            return redirect()->back()->with('success', 'Item removed from cart!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function checkout()
    {
        $cartItems = $this->cartService->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $cartTotal = $this->cartService->getCartTotal();

        return view('cart.checkout', compact('cartItems', 'cartTotal'));
    }

    public function processCheckout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to place an order.');
        }

        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $cartItems = $this->cartService->getCartItems();
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Your cart is empty!');
            }

            // Check stock availability
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                    throw new \Exception("Not enough stock for {$cartItem->product->name}");
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $this->cartService->getCartTotal(),
                'status' => 'pending',
                'shipping_address' => $request->shipping_address
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_time' => $cartItem->product->price
                ]);

                // Update product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            $this->cartService->clearCart();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'redirect_url' => route('orders.show', $order->id)
                ]);
            }

            return redirect()->route('orders.show', $order->id)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // API method for getting cart data
    public function getCartData(Request $request)
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();
        $cartCount = $this->cartService->getCartCount();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_image' => $item->product->image_url,
                        'product_price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'options' => $item->product_options
                    ];
                }),
                'total' => $cartTotal,
                'count' => $cartCount
            ]
        ]);
    }
}