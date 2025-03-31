<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Dashboard route
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/checkout', [CartController::class, 'processCheckout'])->name('cart.process');

// Subscription routes
Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscriptions.index');

// Authentication routes (provided by Jetstream)
// These include login, register, password reset, etc.

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    // Subscription management
    Route::post('/subscriptions', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::get('/profile/subscriptions', [SubscriptionController::class, 'userSubscriptions'])->name('profile.subscriptions');
    Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
});

// API Routes (temporary solution)
Route::prefix('api')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Product routes
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{id}', [ProductController::class, 'apiShow']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // Order routes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
    });
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin authentication
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    
    // Protected admin routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Product management
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        
        // Category management
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Order management
        Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        
        // Subscription management
        Route::get('/subscriptions/plans', [SubscriptionController::class, 'adminIndex'])->name('subscriptions.plans');
        Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
        Route::put('/subscriptions/plans/{id}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
        Route::delete('/subscriptions/plans/{id}', [SubscriptionController::class, 'destroyPlan'])->name('subscriptions.plans.destroy');
        Route::get('/subscriptions', [SubscriptionController::class, 'adminSubscriptions'])->name('subscriptions.index');
    });
});

