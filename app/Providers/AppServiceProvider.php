<?php

namespace App\Providers;

use App\Services\AffiliateService;
use App\Services\CurrencyService;
use App\Services\DropshipService;
use App\Services\EasyPostService;
use App\Services\LoyaltyService;
use App\Services\PayPalService;
use App\Services\ReturnRefundService;
use App\Services\StripeService;
use EasyPost\EasyPostClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class, function () {
            $config = config('services.open_exchange');

            return new CurrencyService(
                appId: $config['app_id'],
                baseUrl: rtrim($config['base_url'], '/'),
                baseCurrency: $config['base_currency'],
                cacheTtl: $config['cache_ttl'],
            );
        });

        $this->app->singleton(StripeService::class, function () {
            return new StripeService(
                stripe: new StripeClient(config('services.stripe.secret')),
                webhookSecret: config('services.stripe.webhook_secret'),
            );
        });

        $this->app->singleton(PayPalService::class, function () {
            $mode = config('services.paypal.mode', 'sandbox');

            return new PayPalService(
                clientId: config('services.paypal.client_id'),
                clientSecret: config('services.paypal.client_secret'),
                baseUrl: config("services.paypal.{$mode}_base_url"),
            );
        });

        $this->app->singleton(EasyPostService::class, function () {
            return new EasyPostService(
                client: new EasyPostClient(config('services.easypost.api_key')),
            );
        });

        $this->app->singleton(DropshipService::class);
        $this->app->singleton(AffiliateService::class);
        $this->app->singleton(LoyaltyService::class);
        $this->app->singleton(ReturnRefundService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('payment', function (Request $request) {
            return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        Vite::prefetch(concurrency: 3);
    }
}
