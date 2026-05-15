<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'open_exchange' => [
        'app_id' => env('OPEN_EXCHANGE_APP_ID', 'YOUR_OPEN_EXCHANGE_APP_ID'),
        'base_url' => env('OPEN_EXCHANGE_BASE_URL', 'https://openexchangerates.org/api'),
        'base_currency' => env('OPEN_EXCHANGE_BASE_CURRENCY', 'USD'),
        'cache_ttl' => (int) env('OPEN_EXCHANGE_CACHE_TTL', 3600),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY', 'pk_test_YOUR_STRIPE_PUBLISHABLE_KEY'),
        'secret' => env('STRIPE_SECRET', 'sk_test_YOUR_STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', 'YOUR_PAYPAL_CLIENT_ID_HERE'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', 'YOUR_PAYPAL_CLIENT_SECRET_HERE'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'sandbox_base_url' => env('PAYPAL_SANDBOX_BASE_URL', 'https://api-m.sandbox.paypal.com'),
        'live_base_url' => env('PAYPAL_LIVE_BASE_URL', 'https://api-m.paypal.com'),
    ],

    'easypost' => [
        'api_key' => env('EASYPOST_API_KEY', 'EZTKXXXXXXXXXXXXXXXXXXXXXXXX'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', 'http://localhost:8000/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', 'YOUR_FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', 'YOUR_FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL', 'http://localhost:8000/auth/facebook/callback'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
