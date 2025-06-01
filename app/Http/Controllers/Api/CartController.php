<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cartItem = $this->cartService->addToCart(
                $request->product_id,
                $request->quantity,
                $request->options ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'data' => [
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal(),
                    'item' => [
                        'id' => $cartItem->id,
                        'product_id' => $cartItem->product_id,
                        'product_name' => $cartItem->product->name,
                        'quantity' => $cartItem->quantity,
                        'subtotal' => $cartItem->subtotal
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), ['quantity' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->quantity == 0) {
                $this->cartService->removeFromCart($itemId);
                $message = 'Item removed from cart';
            } else {
                $cartItem = $this->cartService->updateCartItem($itemId, $request->quantity);
                $message = 'Cart updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function remove($itemId)
    {
        try {
            $this->cartService->removeFromCart($itemId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => [
                    'cart_count' => $this->cartService->getCartCount(),
                    'cart_total' => $this->cartService->getCartTotal()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function clear()
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => [
                    'cart_count' => 0,
                    'cart_total' => 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $cartItems = $this->cartService->getCartItems();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Check stock availability
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for {$cartItem->product->name}"
                    ], 400);
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $request->user()->id,
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

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'total_amount' => $order->total_amount
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}