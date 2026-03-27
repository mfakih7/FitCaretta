<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\HomepageSlideController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\Reports\OrdersReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\StorefrontController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
Route::get('/new-arrivals', [StorefrontController::class, 'newArrivals'])->name('shop.new');
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

Route::get('/debug/mail-test', function () {
    abort_unless(app()->environment('local'), 404);

    $token = (string) request()->query('token', '');
    abort_unless($token !== '' && $token === (string) env('MAIL_DEBUG_TOKEN'), 403);

    $to = (string) request()->query('to', env('MAIL_FROM_ADDRESS', ''));
    abort_unless($to !== '', 400, 'Missing ?to=');

    Log::info('Debug mail-test requested', [
        'to' => $to,
        'mailer' => (string) config('mail.default'),
        'host' => (string) config('mail.mailers.smtp.host'),
        'port' => (string) config('mail.mailers.smtp.port'),
        'encryption' => (string) config('mail.mailers.smtp.encryption'),
        'from' => (string) config('mail.from.address'),
    ]);

    try {
        Mail::raw('FitCaretta SMTP test email.', function ($message) use ($to) {
            $message->to($to)->subject('FitCaretta SMTP test');
        });
    } catch (\Throwable $e) {
        Log::error('Debug mail-test failed', [
            'to' => $to,
            'exception' => get_class($e),
            'error' => $e->getMessage(),
        ]);
        throw $e;
    }

    return response()->json(['ok' => true, 'to' => $to]);
})->name('debug.mail-test');

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

            Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

            Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::resource('homepage-slides', HomepageSlideController::class)->except(['show']);

            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('product-types', ProductTypeController::class)->except(['show']);
            Route::resource('sizes', SizeController::class)->except(['show']);
            Route::resource('colors', ColorController::class)->except(['show']);
            Route::resource('products', ProductController::class)->except(['show']);
            Route::patch('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::resource('discounts', DiscountController::class)->except(['show']);
            Route::resource('coupons', CouponController::class)->except(['show']);
            Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
            Route::get('reports/orders', [OrdersReportController::class, 'index'])->name('reports.orders.index');
            Route::get('reports/orders/export', [OrdersReportController::class, 'exportCsv'])->name('reports.orders.export');
            Route::delete(
                'products/{product}/gallery-images/{image}',
                [ProductController::class, 'destroyGalleryImage']
            )->name('products.gallery-images.destroy');
        });
    });
