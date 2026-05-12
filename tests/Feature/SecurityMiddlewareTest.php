<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SecurityMiddlewareTest extends TestCase
{
    public function test_security_headers_are_applied_to_responses(): void
    {
        $response = $this->get('/up');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://js.stripe.com', $csp);
        $this->assertStringContainsString('https://paypal.com', $csp);
        $this->assertStringContainsString('https://*.paypal.com', $csp);
        $this->assertStringContainsString('https://tawk.to', $csp);
        $this->assertStringContainsString('https://*.tawk.to', $csp);
        $this->assertStringContainsString('https://cloudinary.com', $csp);
        $this->assertStringContainsString('https://*.cloudinary.com', $csp);
    }

    public function test_currency_and_language_cookies_are_stored_in_session(): void
    {
        $response = $this
            ->withCookie('currency', 'idr')
            ->withCookie('language', 'id')
            ->get('/login');

        $response->assertOk();
        $response->assertSessionHas('currency', 'IDR');
        $response->assertSessionHas('language', 'id');

        $this->assertSame('id', App::getLocale());
    }

    public function test_task_11_rate_limiters_are_attached_to_target_routes(): void
    {
        $this->assertUriHasMiddleware('login', 'POST', 'throttle:auth');
        $this->assertUriHasMiddleware('register', 'POST', 'throttle:auth');
        $this->assertRouteHasMiddleware('password.email', 'POST', 'throttle:auth');
        $this->assertRouteHasMiddleware('password.update', 'POST', 'throttle:auth');

        $this->assertRouteHasMiddleware('payment.stripe.intent', 'POST', 'throttle:payment');
        $this->assertRouteHasMiddleware('payment.paypal.create', 'POST', 'throttle:payment');
        $this->assertRouteHasMiddleware('payment.paypal.capture', 'POST', 'throttle:payment');

        $this->assertRouteHasMiddleware('api.shipping.rates', 'POST', 'throttle:api');
        $this->assertRouteHasMiddleware('api.currency.rates', 'GET', 'throttle:api');
    }

    private function assertRouteHasMiddleware(string $name, string $method, string $middleware): void
    {
        $route = collect(app('router')->getRoutes()->getRoutesByName())->get($name);

        $this->assertNotNull($route, "Route [{$name}] was not found.");
        $this->assertContains($method, $route->methods(), "Route [{$name}] does not support {$method}.");
        $this->assertContains($middleware, $route->gatherMiddleware(), "Route [{$name}] is missing [{$middleware}].");
    }

    private function assertUriHasMiddleware(string $uri, string $method, string $middleware): void
    {
        $route = collect(app('router')->getRoutes())->first(
            fn ($route) => $route->uri() === $uri && in_array($method, $route->methods(), true)
        );

        $this->assertNotNull($route, "Route [{$method} {$uri}] was not found.");
        $this->assertContains($middleware, $route->gatherMiddleware(), "Route [{$method} {$uri}] is missing [{$middleware}].");
    }
}
