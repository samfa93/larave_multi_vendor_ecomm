<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\Cart\CouponController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\Order\CheckoutController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Product\CategoryController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Product\ReviewController;
use App\Http\Controllers\Api\V1\User\AddressController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\Vendor\VendorController;
use App\Http\Controllers\Api\V1\Wishlist\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // Home & Content
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/contact', [HomeController::class, 'contact']);
    Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter']);

    // Products & Catalog (Public)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/products', [CategoryController::class, 'products']);
    Route::get('/brands', [ProductController::class, 'brands']);
    Route::get('/flash-sales', [ProductController::class, 'flashSales']);
    Route::get('/featured-products', [ProductController::class, 'featuredProducts']);
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

    // Vendors (Public)
    Route::get('/vendors', [VendorController::class, 'index']);
    Route::get('/vendors/{id}', [VendorController::class, 'show']);
    Route::get('/vendors/{id}/products', [VendorController::class, 'products']);

    // Payment Webhooks (Public)
    Route::post('/payments/webhook/paypal', [PaymentController::class, 'paypalWebhook']);
    Route::post('/payments/webhook/stripe', [PaymentController::class, 'stripeWebhook']);
    Route::post('/payments/webhook/razorpay', [PaymentController::class, 'razorpayWebhook']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::post('/logout', [LoginController::class, 'logout']);

        // User Profile
        Route::get('/user/profile', [ProfileController::class, 'show']);
        Route::put('/user/profile', [ProfileController::class, 'update']);
        Route::put('/user/password', [ProfileController::class, 'updatePassword']);
        Route::post('/user/avatar', [ProfileController::class, 'updateAvatar']);

        // Addresses
        Route::apiResource('/addresses', AddressController::class);

        // Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);

        // Coupon
        Route::post('/cart/coupon', [CouponController::class, 'apply']);
        Route::delete('/cart/coupon', [CouponController::class, 'remove']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

        // Reviews
        Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

        // Checkout & Orders
        Route::get('/shipping-methods/{addressId}', [CheckoutController::class, 'shippingMethods']);
        Route::post('/checkout/billing-info', [CheckoutController::class, 'setBillingInfo']);
        Route::get('/checkout/summary', [CheckoutController::class, 'summary']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/orders/{id}/track', [OrderController::class, 'track']);

        // Payments
        Route::post('/payments/paypal/create', [PaymentController::class, 'createPaypalPayment']);
        Route::post('/payments/stripe/create', [PaymentController::class, 'createStripePayment']);
        Route::post('/payments/razorpay/create', [PaymentController::class, 'createRazorpayPayment']);
        Route::post('/payments/cod', [PaymentController::class, 'codPayment']);

        // Vendor routes (for users with vendor role)
        Route::prefix('vendor')->middleware('user_role:vendor')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\Vendor\VendorDashboardController::class, 'index']);
            Route::get('/profile', [\App\Http\Controllers\Api\V1\Vendor\VendorProfileController::class, 'show']);
            Route::put('/profile', [\App\Http\Controllers\Api\V1\Vendor\VendorProfileController::class, 'update']);
            Route::apiResource('/products', \App\Http\Controllers\Api\V1\Vendor\VendorProductController::class);
            Route::get('/orders', [\App\Http\Controllers\Api\V1\Vendor\VendorOrderController::class, 'index']);
            Route::get('/orders/{id}', [\App\Http\Controllers\Api\V1\Vendor\VendorOrderController::class, 'show']);
            Route::put('/orders/{id}', [\App\Http\Controllers\Api\V1\Vendor\VendorOrderController::class, 'update']);
        });
    });
});
