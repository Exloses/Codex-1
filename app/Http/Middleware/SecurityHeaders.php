<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => $this->contentSecurityPolicy(),
        ];

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://paypal.com https://*.paypal.com https://tawk.to https://*.tawk.to",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://tawk.to https://*.tawk.to",
            "img-src 'self' data: blob: https://cloudinary.com https://*.cloudinary.com https://res.cloudinary.com https://*.stripe.com https://paypal.com https://*.paypal.com https://tawk.to https://*.tawk.to",
            "font-src 'self' data: https://fonts.bunny.net https://fonts.gstatic.com https://tawk.to https://*.tawk.to",
            "connect-src 'self' https://api.stripe.com https://paypal.com https://*.paypal.com https://tawk.to https://*.tawk.to wss://*.tawk.to https://cloudinary.com https://*.cloudinary.com",
            "frame-src 'self' https://js.stripe.com https://hooks.stripe.com https://paypal.com https://*.paypal.com https://tawk.to https://*.tawk.to",
            "media-src 'self' data: blob: https://cloudinary.com https://*.cloudinary.com",
            "worker-src 'self' blob:",
            "manifest-src 'self'",
        ]);
    }
}
