<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\FaqController;
use App\Http\Controllers\Storefront\InvoiceController;
use App\Http\Controllers\Storefront\LoyaltyController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\NotificationController;
use App\Http\Controllers\Storefront\PaymentController;
use App\Http\Controllers\Storefront\PreferenceController;
use App\Http\Controllers\Storefront\PriceAlertController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ProductQAController;
use App\Http\Controllers\Storefront\ReturnController;
use App\Http\Controllers\Storefront\ReviewController;
use App\Http\Controllers\Storefront\StockNotificationController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\SupportTicketController;
use App\Http\Controllers\Storefront\TrackingController;
use App\Http\Controllers\Storefront\WishlistController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorFinanceController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorSettingsController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook')
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook');

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/track-order', [TrackingController::class, 'index'])->name('track.index');
Route::post('/track-order', [TrackingController::class, 'track'])->name('track.order');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/affiliate', [AffiliateController::class, 'landing'])->name('affiliate.landing');
Route::get('/ref/{code}', [AffiliateController::class, 'track'])->name('affiliate.track');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::post('/preferences/currency', [PreferenceController::class, 'setCurrency'])->name('preferences.currency');
Route::post('/preferences/language', [PreferenceController::class, 'setLanguage'])->name('preferences.language');

Route::post('/api/shipping/rates', [ShippingController::class, 'getRates'])->name('api.shipping.rates');
Route::get('/api/currency/rates', [CurrencyController::class, 'getRates'])->name('api.currency.rates');

Route::post('/webhook/stripe', [PaymentController::class, 'stripeWebhook'])
    ->withoutMiddleware([ValidateCsrfToken::class, VerifyCsrfToken::class])
    ->name('webhook.stripe');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/redeem-points', [CheckoutController::class, 'redeemPoints'])->name('checkout.redeem-points');

    Route::post('/payment/stripe/intent', [PaymentController::class, 'createStripeIntent'])->name('payment.stripe.intent');
    Route::post('/payment/paypal/create', [PaymentController::class, 'createPayPalOrder'])->name('payment.paypal.create');
    Route::post('/payment/paypal/capture', [PaymentController::class, 'capturePayPalOrder'])->name('payment.paypal.capture');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [AccountController::class, 'orderDetail'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');
        Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/loyalty-points', [LoyaltyController::class, 'index'])->name('loyalty');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });

    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('/notifications/stock', [StockNotificationController::class, 'store'])->name('notifications.stock.store');
    Route::post('/notifications/price-alert', [PriceAlertController::class, 'store'])->name('notifications.price-alert.store');
    Route::post('/products/{product}/questions', [ProductQAController::class, 'store'])->name('products.questions.store');
    Route::post('/questions/{question}/answers', [ProductQAController::class, 'answer'])->name('questions.answers.store');

    Route::prefix('affiliate')->name('affiliate.')->group(function () {
        Route::post('/register', [AffiliateController::class, 'register'])->name('register');
        Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
        Route::get('/commissions', [AffiliateController::class, 'commissions'])->name('commissions');
        Route::post('/payout-methods', [AffiliateController::class, 'storePayoutMethod'])->name('payout-methods.store');
        Route::post('/payouts', [AffiliateController::class, 'requestPayout'])->name('payouts.store');
        Route::get('/payouts', [AffiliateController::class, 'payoutHistory'])->name('payouts.index');
        Route::post('/generate-link', [AffiliateController::class, 'generateLink'])->name('generate-link');
    });
});

Route::post('/checkout/guest', [CheckoutController::class, 'guestStore'])->name('checkout.guest');

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', VendorProductController::class);
    Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{dropship}/confirm', [VendorOrderController::class, 'confirm'])->name('orders.confirm');
    Route::put('/orders/{dropship}/ship', [VendorOrderController::class, 'markShipped'])->name('orders.ship');
    Route::get('/finance', [VendorFinanceController::class, 'index'])->name('finance');
    Route::post('/finance/withdraw', [VendorFinanceController::class, 'requestWithdrawal'])->name('finance.withdraw');
    Route::get('/settings', [VendorSettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [VendorSettingsController::class, 'update'])->name('settings.update');
});

Route::post('/vendor/register', [VendorDashboardController::class, 'registerVendor'])
    ->middleware('auth')
    ->name('vendor.apply');
