<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get statistics for dashboard
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'low_stock_products' => Product::where('stock_quantity', '&lt;', 20)
                ->where('stock_quantity', '>', 0)
                ->get(),
            'out_of_stock_products' => Product::where('stock_quantity', 0)->count(),
            'revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
        
        // Get price distribution for chart
        $priceRanges = [
            '0-200' => Product::whereBetween('price', [0, 200])->count(),
            '201-400' => Product::whereBetween('price', [201, 400])->count(),
            '401-600' => Product::whereBetween('price', [401, 600])->count(),
            '601-800' => Product::whereBetween('price', [601, 800])->count(),
            '801-2000' => Product::whereBetween('price', [801, 2000])->count(),
            '2000+' => Product::where('price', '>', 2000)->count(),
        ];
        
        return view('admin.dashboard', compact('stats', 'priceRanges'));
    }

    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle an admin login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the admin out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

