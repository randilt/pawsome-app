<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected function getCartQuery()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id());
        }
        
        return CartItem::where('session_id', Session::getId());
    }

    public function addToCart(int $productId, int $quantity = 1, array $options = []): CartItem
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock_quantity < $quantity) {
            throw new \Exception('Not enough stock available');
        }

        $cartItem = $this->getCartQuery()
            ->where('product_id', $productId)
            ->where('product_options', json_encode($options))
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock_quantity < $newQuantity) {
                throw new \Exception('Not enough stock available');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'session_id' => Auth::check() ? null : Session::getId(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'product_id' => $productId,
                'quantity' => $quantity,
                'product_options' => $options
            ]);
        }

        return $cartItem;
    }

    public function updateCartItem(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->getCartQuery()->findOrFail($cartItemId);
        
        if ($quantity <= 0) {
            $cartItem->delete();
            throw new \Exception('Item removed from cart');
        }

        if ($cartItem->product->stock_quantity < $quantity) {
            throw new \Exception('Not enough stock available');
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    public function removeFromCart(int $cartItemId): bool
    {
        $cartItem = $this->getCartQuery()->findOrFail($cartItemId);
        return $cartItem->delete();
    }

    public function getCartItems()
    {
        return $this->getCartQuery()->with('product')->get();
    }

    public function getCartTotal(): float
    {
        return $this->getCartItems()->sum('subtotal');
    }

    public function getCartCount(): int
    {
        return $this->getCartItems()->sum('quantity');
    }

    public function clearCart(): bool
    {
        return $this->getCartQuery()->delete();
    }

    public function mergeGuestCartToUser(int $userId): void
    {
        if (!Session::has('guest_cart_merged')) {
            $guestItems = CartItem::where('session_id', Session::getId())->get();
            
            foreach ($guestItems as $guestItem) {
                $existingItem = CartItem::where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_options', $guestItem->product_options)
                    ->first();

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $guestItem->quantity
                    ]);
                } else {
                    $guestItem->update([
                        'user_id' => $userId,
                        'session_id' => null
                    ]);
                }
            }

            // Clean up any remaining guest items
            CartItem::where('session_id', Session::getId())->delete();
            Session::put('guest_cart_merged', true);
        }
    }
}