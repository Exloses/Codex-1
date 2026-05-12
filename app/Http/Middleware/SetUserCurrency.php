<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetUserCurrency
{
    /**
     * @var array<int, string>
     */
    private const AVAILABLE_CURRENCIES = ['USD', 'EUR', 'GBP', 'AUD', 'SGD', 'MYR', 'IDR'];

    public function handle(Request $request, Closure $next): Response
    {
        $currency = $this->normalizeCurrency($request->cookie('currency', 'USD'));
        $language = $this->normalizeLanguage($request->cookie('language', 'en'));

        $request->session()->put([
            'currency' => $currency,
            'language' => $language,
        ]);

        App::setLocale($language);

        return $next($request);
    }

    private function normalizeCurrency(mixed $currency): string
    {
        $currency = strtoupper((string) $currency);

        return in_array($currency, self::AVAILABLE_CURRENCIES, true) ? $currency : 'USD';
    }

    private function normalizeLanguage(mixed $language): string
    {
        $language = strtolower((string) $language);

        return preg_match('/^[a-z]{2}(-[a-z]{2})?$/', $language) === 1 ? $language : 'en';
    }
}
