<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
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

