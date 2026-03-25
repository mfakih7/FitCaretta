<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/men', [StorefrontController::class, 'men'])->name('shop.men');
Route::get('/women', [StorefrontController::class, 'women'])->name('shop.women');
Route::get('/categories/{slug}', [StorefrontController::class, 'category'])->name('shop.category');
Route::get('/products/{slug}', [StorefrontController::class, 'productDetails'])->name('products.show');
Route::get('/search', [StorefrontController::class, 'search'])->name('search');
Route::get('/offers', [StorefrontController::class, 'offers'])->name('offers');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{key}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/orders/success/{token}', [OrderController::class, 'success'])->name('orders.success');
Route::get('/orders/{token}', [OrderController::class, 'show'])->name('orders.show');

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
        });

        Route::middleware(['auth:admin', 'admin'])->group(function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
            Route::get('/', fn () => redirect()->route('admin.categories.index'))->name('dashboard');

            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('product-types', ProductTypeController::class)->except(['show']);
            Route::resource('sizes', SizeController::class)->except(['show']);
            Route::resource('colors', ColorController::class)->except(['show']);
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('discounts', DiscountController::class)->except(['show']);
            Route::resource('coupons', CouponController::class)->except(['show']);
            Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
            Route::delete(
                'products/{product}/gallery-images/{image}',
                [ProductController::class, 'destroyGalleryImage']
            )->name('products.gallery-images.destroy');
        });
    });
