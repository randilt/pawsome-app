<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle an admin authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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

    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get statistics for dashboard
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalCustomers = User::count();
        $totalProducts = Product::count();
        
        // Get recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get popular products
        $popularProducts = Product::withCount(['orderItems as sales_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->with('category')
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'popularProducts'
        ));
    }
}

