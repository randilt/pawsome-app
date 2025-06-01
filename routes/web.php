<?php
// routes/web.php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Health check route
Route::get('/health-check', function () {
    try {
        $checks = [
            'status' => 'OK',
            'timestamp' => now()->toISOString(),
            'app_env' => config('app.env', 'unknown'),
            'app_debug' => config('app.debug', false) ? 'true' : 'false',
            'app_key_set' => config('app.key') ? 'YES' : 'NO',
        ];

        // Test database connection only if config is available
        try {
            if (config('database.default')) {
                DB::connection()->getPdo();
                $checks['database'] = 'connected';
            } else {
                $checks['database'] = 'no config';
            }
        } catch (\Exception $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
        }

        // Test MongoDB extension
        $checks['mongodb_extension'] = extension_loaded('mongodb') ? 'loaded' : 'not loaded';
        
        return response()->json($checks, 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => date('Y-m-d H:i:s')
        ], 500);
    }
});

// Only for debugging
Route::get('/debug-env', function () {
    if (config('app.env') !== 'production') {
        return response()->json([
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_KEY_SET' => env('APP_KEY') ? 'YES' : 'NO',
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'APP_URL' => env('APP_URL'),
        ]);
    }
    return response('Debug only available in non-production', 403);
});

Route::get('/debug-config', function () {
    return response()->json([
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_key' => config('app.key') ? 'SET' : 'NOT SET',
        'db_connection' => config('database.default'),
        'app_url' => config('app.url'),
    ]);
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'byCategory'])->name('categories.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'getCartData'])->name('cart.data');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Subscription routes
Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscriptions.index');

// Dashboard route
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Authentication routes (provided by Jetstream)
// These include login, register, password reset, etc.

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Cart checkout routes (require authentication)
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartController::class, 'processCheckout'])->name('cart.process-checkout');
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Product Review routes (MongoDB)
    Route::post('/products/{id}/reviews', [ProductController::class, 'storeReview'])->name('products.reviews.store');
        
    // Subscription management
    Route::post('/subscriptions', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::get('/profile/subscriptions', [SubscriptionController::class, 'userSubscriptions'])->name('profile.subscriptions');
    Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin authentication
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    
    // Protected admin routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/contacts', function () {
            return view('admin.contacts.index');
        })->name('contacts.index');
        
        // Admin product management (with MongoDB analytics)
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        
        // Product Analytics routes (MongoDB)
        Route::get('/products/{id}/analytics', [ProductController::class, 'showAnalytics'])->name('products.analytics');
        
        // Category management
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Order management (with MongoDB analytics)
        Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'adminShow'])->name('orders.show');
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        
        // Subscription management
        Route::get('/subscriptions/plans', [SubscriptionController::class, 'adminIndex'])->name('subscriptions.plans');
        Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
        Route::put('/subscriptions/plans/{id}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
        Route::delete('/subscriptions/plans/{id}', [SubscriptionController::class, 'destroyPlan'])->name('subscriptions.plans.destroy');
        Route::get('/subscriptions', [SubscriptionController::class, 'adminSubscriptions'])->name('subscriptions.index');
    });
});