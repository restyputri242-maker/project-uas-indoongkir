<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RajaOngkirController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Public shop catalog homepage
Route::get('/', function () {
    $products = Product::where('stock', '>', 0)->orderBy('created_at', 'desc')->get();
    return view('shop', compact('products'));
})->name('shop');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cart Operations (Buyers)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.destroy');

    // Checkout Operations (Buyers)
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'storeCheckout'])->name('checkout.store');

    // Buyer Order History
    Route::get('/orders', [OrderController::class, 'buyerOrders'])->name('buyer.orders');

    // Print Invoice (Buyers & Admins)
    Route::get('/orders/{transaction}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Admin Specific Operations
    Route::middleware('admin')->group(function () {
        // Product CRUD (Admin)
        Route::resource('admin/products', ProductController::class)->names([
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'store' => 'admin.products.store',
            'edit' => 'admin.products.edit',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ])->except(['show']);

        // Order Management (Admin)
        Route::get('/admin/orders', [OrderController::class, 'adminOrders'])->name('admin.orders');
        Route::patch('/admin/orders/{transaction}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    });
});

// AJAX RajaOngkir endpoints (Internal APIs for cart/checkout)
Route::prefix('api/rajaongkir')->group(function () {
    Route::get('/provinces', [RajaOngkirController::class, 'getProvinces']);
    Route::get('/cities/{provinceId}', [RajaOngkirController::class, 'getCities']);
    Route::post('/cost', [RajaOngkirController::class, 'calculateCost']);
});