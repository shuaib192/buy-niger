<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Web Routes
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorProductController;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\UserWishlistController;

/*
|--------------------------------------------------------------------------
| Public Shop Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CatalogController;

// HOME
Route::get('/', [CatalogController::class, 'home'])->name('home');

// Shop Pages
Route::get('/about', [CatalogController::class, 'about'])->name('about');
Route::get('/contact', [CatalogController::class, 'contact'])->name('contact');
Route::post('/contact', [CatalogController::class, 'sendContact'])->name('contact.send');
Route::match(['get', 'post'], '/track-order', [CatalogController::class, 'trackOrder'])->name('track.order');

// Policies (using static closures for simplicity as they just return views)
Route::get('/privacy', function() { return view('shop.privacy'); })->name('privacy');
Route::get('/terms', function() { return view('shop.terms'); })->name('terms');
Route::get('/vendor-policy', function() { return view('shop.vendor-policy'); })->name('vendor.policy');
Route::get('/refund-policy', function() { return view('shop.refund-policy'); })->name('refund.policy');

// CATALOG - Using the /products bypass
Route::get('/products', [CatalogController::class, 'index'])->name('catalog');

Route::get('/category/{category}', [CatalogController::class, 'category'])->name('category');
Route::get('/product/{slug}', [CatalogController::class, 'product'])->name('product.detail');

// REDIRECTS
Route::get('/shop', function() {
    return redirect()->route('catalog');
});

// Vendor Storefront
use App\Http\Controllers\StoreController;
Route::get('/stores', [StoreController::class, 'index'])->name('stores');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('store.show');
Route::get('/search/suggestions', [ShopController::class, 'suggestions'])->name('search.suggestions');

// Cart Routes - bypass cursed /cart URL
Route::get('/my-basket', [BasketController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [BasketController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{item}', [BasketController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [BasketController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [BasketController::class, 'count'])->name('cart.count');
Route::post('/cart/clear', [BasketController::class, 'clear'])->name('cart.clear');

Route::get('/cart', function() {
    return redirect()->route('cart.index');
});

// Wishlist Routes - bypass cursed /wishlist URL
Route::get('/my-wishlist', [UserWishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [UserWishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/{productId}', [UserWishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/move/{productId}', [UserWishlistController::class, 'moveToCart'])->name('wishlist.move');

Route::get('/wishlist', function() {
    return redirect()->route('wishlist.index');
});

// Checkout Routes (Auth Required)
Route::middleware('auth')->group(function () {
    // Checkout - bypass cursed URLs
    Route::get('/checkout-order-now', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout-order-now', [OrderController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-coupon', [OrderController::class, 'applyCoupon'])->name('checkout.applyCoupon');

    Route::get('/checkout', function() {
        return redirect()->route('checkout.index');
    });

    Route::get('/order/confirmation/{orderNumber}', [OrderController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/my-orders', [CheckoutController::class, 'orders'])->name('orders.index');
    Route::get('/order/{order}', [CheckoutController::class, 'orderDetail'])->name('orders.detail');
});

// Payment Routes
use App\Http\Controllers\PaymentController;

Route::middleware('auth')->group(function () {
    Route::get('/pay/{order}', [PaymentController::class, 'paymentPage'])->name('payment.page');
    Route::post('/pay/{order}', [PaymentController::class, 'initializePayment'])->name('payment.initialize');
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
});

// Notification Routes
use App\Http\Controllers\NotificationController;

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Chatbot Routes
use App\Http\Controllers\ChatbotController;

Route::middleware('auth')->group(function () {
    Route::get('/chatbot/open', [ChatbotController::class, 'open'])->name('chatbot.open');
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
    Route::get('/chatbot/history/{session}', [ChatbotController::class, 'history'])->name('chatbot.history');
});

// Paystack Webhook (no auth)
Route::post('/webhook/paystack', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Vendor Application (for existing logged-in customers)
Route::middleware('auth')->group(function () {
    Route::get('/become-a-vendor', [ShopController::class, 'showVendorApplication'])->name('vendor.apply');
    Route::post('/become-a-vendor', [ShopController::class, 'submitVendorApplication'])->name('vendor.apply.submit');
});

// Debug Mail Config
Route::get('/debug-mail-config', function() {
    return [
        'app_name' => config('app.name'),
        'mail_from_address' => config('mail.from.address'),
        'mail_from_name' => config('mail.from.name'),
        'mail_host' => config('mail.mailers.smtp.host'),
        'mail_port' => config('mail.mailers.smtp.port'),
    ];
});

// SECRET MIGRATE ROUTE
Route::get('/run-migration-secret-777', function() {
    try {
        if (function_exists('opcache_reset')) { opcache_reset(); }
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return "<pre>Migration Successful:\n" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<pre>Migration Failed:\n" . $e->getMessage() . "</pre>";
    }
});


// Redirect /home to /

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Customer Registration
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Vendor Registration
    Route::get('/vendor/register', [AuthController::class, 'showVendorRegister'])->name('vendor.register');
    Route::post('/vendor/register', [AuthController::class, 'vendorRegister'])->name('vendor.register.submit');

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification (accessible without login)
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.show');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/verify-email/resend', [AuthController::class, 'resendVerificationCode'])->name('verification.resend');

// Logout (requires auth)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:4'])->prefix('account')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::post('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [CustomerController::class, 'updatePassword'])->name('password.update');
    Route::get('/addresses', [CustomerController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{id}', [CustomerController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{id}/default', [CustomerController::class, 'setDefaultAddress'])->name('addresses.default');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Messaging
    Route::post('/messages/start/{vendor}', [MessageController::class, 'startConversation'])->name('messages.start');
    Route::get('/messages', [MessageController::class, 'indexCustomer'])->name('messages.index');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{id}', [MessageController::class, 'send'])->name('messages.send');
    
    // Disputes
    Route::post('/orders/{order}/dispute', [CustomerController::class, 'storeDispute'])->name('dispute.store');

    // Order Cancellation
    Route::post('/orders/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('orders.cancel');
});

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:3', 'vendor.approved'])->prefix('vendor')->name('vendor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    
    // Inventory
    Route::get('/inventory', [VendorController::class, 'inventory'])->name('inventory');
    Route::post('/inventory/update-stock', [VendorController::class, 'updateStock'])->name('inventory.update');

    // Analytics
    Route::get('/analytics', [VendorController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/export', [VendorController::class, 'exportAnalytics'])->name('analytics.export');

    // Products
    Route::get('/products', [VendorProductController::class, 'index'])->name('products');
    Route::post('/products/bulk', [VendorProductController::class, 'bulkAction'])->name('products.bulk');
    Route::get('/products/create', [VendorProductController::class, 'create'])->name('products.create');
    Route::post('/products', [VendorProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [VendorProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [VendorProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [VendorProductController::class, 'destroy'])->name('products.destroy');

    // Coupons
    Route::get('/coupons', [VendorController::class, 'coupons'])->name('coupons');
    Route::post('/coupons', [VendorController::class, 'storeCoupon'])->name('coupons.store');
    Route::delete('/coupons/{id}', [VendorController::class, 'destroyCoupon'])->name('coupons.destroy');
    Route::post('/coupons/toggle/{id}', [VendorController::class, 'toggleCouponStatus'])->name('coupons.toggle');
    
    // Orders
    Route::get('/orders', [VendorController::class, 'orders'])->name('orders');
    Route::get('/orders/export', [VendorController::class, 'exportOrders'])->name('orders.export');
    Route::get('/orders/{id}', [VendorController::class, 'orderDetail'])->name('orders.show');
    Route::post('/orders/{id}/status', [VendorController::class, 'updateOrderStatus'])->name('orders.status');
    
    // Settings
    Route::get('/settings', [VendorController::class, 'settings'])->name('settings');
    Route::post('/settings', [VendorController::class, 'updateSettings'])->name('settings.update');

    // Finances & Payouts
    Route::get('/finances', [VendorController::class, 'finances'])->name('finances');
    Route::post('/payouts/request', [VendorController::class, 'requestPayout'])->name('payouts.request');

    // Profile
    Route::get('/profile', [VendorController::class, 'profile'])->name('profile');
    Route::post('/profile', [VendorController::class, 'updateProfile'])->name('profile.update');

    // Messaging
    Route::get('/messages', [MessageController::class, 'indexVendor'])->name('messages.index');
    Route::post('/messages/start/{userId}', [MessageController::class, 'startConversationFromVendor'])->name('messages.start');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{id}', [MessageController::class, 'send'])->name('messages.send');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:2'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Vendors
    Route::get('/vendors', [SuperAdminController::class, 'vendors'])->name('vendors');
    Route::get('/vendors/{vendor}', [SuperAdminController::class, 'vendorShow'])->name('vendors.show');
    Route::post('/vendors/{vendor}/status', [SuperAdminController::class, 'updateVendorStatus'])->name('vendors.status');
    Route::post('/vendors/{vendor}/kyc', [SuperAdminController::class, 'updateKycStatus'])->name('vendors.kyc');

    // Users
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [SuperAdminController::class, 'userShow'])->name('users.show');
    Route::post('/users/{user}/ban', [SuperAdminController::class, 'toggleUserBan'])->name('users.ban');

    // Orders
    Route::get('/orders', [SuperAdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [SuperAdminController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{order}/status', [SuperAdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Export
    Route::get('/users/export', [SuperAdminController::class, 'exportUsers'])->name('users.export');

    // Products
    Route::get('/products', [SuperAdminController::class, 'products'])->name('products');
    Route::post('/products/{product}/status', [SuperAdminController::class, 'updateProductStatus'])->name('products.status');
    Route::post('/products/{product}/feature', [SuperAdminController::class, 'toggleProductFeature'])->name('products.feature');

    // Disputes
    Route::get('/disputes', [SuperAdminController::class, 'disputes'])->name('disputes');
    Route::get('/disputes/{dispute}', [SuperAdminController::class, 'disputeShow'])->name('disputes.show');
    Route::post('/disputes/{dispute}/update', [SuperAdminController::class, 'updateDisputeStatus'])->name('disputes.update');
    Route::post('/disputes/{dispute}/message', [SuperAdminController::class, 'addDisputeMessage'])->name('disputes.message');

    // Contact Messages
    Route::get('/messages', [SuperAdminController::class, 'messages'])->name('messages');
    
    // Transactions
    Route::get('/transactions', [SuperAdminController::class, 'transactions'])->name('transactions');
    
    // Order Tracking
    Route::post('/track', [SuperAdminController::class, 'trackOrder'])->name('track');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:1'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/optimize', [SuperAdminController::class, 'optimizeSystem'])->name('optimize');
    // User Management
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [SuperAdminController::class, 'userShow'])->name('users.show');
    Route::post('/users/{user}/ban', [SuperAdminController::class, 'toggleUserBan'])->name('users.ban');

    // Vendor Management
    Route::get('/vendors', [SuperAdminController::class, 'vendors'])->name('vendors');
    Route::get('/vendors/{vendor}', [SuperAdminController::class, 'vendorShow'])->name('vendors.show');
    Route::post('/vendors/{vendor}/status', [SuperAdminController::class, 'updateVendorStatus'])->name('vendors.status');
    Route::post('/vendors/{vendor}/kyc', [SuperAdminController::class, 'updateKycStatus'])->name('vendors.kyc');
    Route::get('/payouts', [SuperAdminController::class, 'payouts'])->name('payouts');
    Route::post('/payouts/{id}/status', [SuperAdminController::class, 'updatePayoutStatus'])->name('payouts.status');

    // Export
    Route::get('/users/export', [SuperAdminController::class, 'exportUsers'])->name('users.export');

    // Product Moderation
    Route::get('/products', [SuperAdminController::class, 'products'])->name('products');
    Route::post('/products/{product}/status', [SuperAdminController::class, 'updateProductStatus'])->name('products.status');
    Route::post('/products/{product}/feature', [SuperAdminController::class, 'toggleProductFeature'])->name('products.feature');

    // Order Monitoring
    Route::get('/orders', [SuperAdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [SuperAdminController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{order}/status', [SuperAdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/orders/track', [SuperAdminController::class, 'trackOrder'])->name('orders.track');

    // Dispute Management
    Route::get('/disputes', [SuperAdminController::class, 'disputes'])->name('disputes');
    Route::get('/disputes/{dispute}', [SuperAdminController::class, 'disputeShow'])->name('disputes.show');
    Route::post('/disputes/{dispute}/update', [SuperAdminController::class, 'updateDisputeStatus'])->name('disputes.update');
    Route::post('/disputes/{dispute}/message', [SuperAdminController::class, 'addDisputeMessage'])->name('disputes.message');

    // AI Control
    Route::get('/ai', [SuperAdminController::class, 'aiControl'])->name('ai');
    Route::get('/ai/settings', [SuperAdminController::class, 'aiSettings'])->name('ai.settings');

    // New Features
    Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
    Route::get('/users/roles', [SuperAdminController::class, 'userRoles'])->name('users.roles');
    Route::get('/settings/payments', [SuperAdminController::class, 'paymentSettings'])->name('settings.payments');
    Route::get('/settings/email', [SuperAdminController::class, 'emailSettings'])->name('settings.email');
    
    // Audit Logs
    Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])->name('audit');

    // Transactions
    Route::get('/transactions', [SuperAdminController::class, 'transactions'])->name('transactions');

    // Contact Messages
    Route::get('/messages', [SuperAdminController::class, 'messages'])->name('messages');

    // Order Tracking
    Route::post('/track', [SuperAdminController::class, 'trackOrder'])->name('track');
    
    // AI Actions
    Route::post('/ai/settings', [SuperAdminController::class, 'updateAiSettings'])->name('ai.settings.update');
    Route::post('/ai/kill-switch', [SuperAdminController::class, 'toggleAiKillSwitch'])->name('ai.killswitch');

    // Email Templates
    Route::resource('email-templates', \App\Http\Controllers\EmailTemplateController::class)->names('email.templates');
    
    // Email Campaigns
    Route::resource('email-campaigns', \App\Http\Controllers\EmailCampaignController::class)->names('email.campaigns');
});
