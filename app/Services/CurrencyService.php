<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;
use Throwable;

class CurrencyService
{
    public function __construct(
        private readonly string $appId,
        private readonly string $baseUrl,
        private readonly string $baseCurrency = 'USD',
        private readonly int $cacheTtl = 3600,
    ) {}

    public function getRates(bool $forceRefresh = false): array
    {
        $cacheKey = 'currency.rates.'.$this->baseCurrency;

        if ($forceRefresh) {
            Cache::store($this->cacheStore())->forget($cacheKey);
        }

        return Cache::store($this->cacheStore())->remember(
            $cacheKey,
            $this->cacheTtl,
            fn () => $this->fetchRates()
        );
    }

    public function convert(float|int|string $amount, string $toCurrency, ?string $fromCurrency = null): float
    {
        $fromCurrency ??= $this->baseCurrency;
        $toCurrency = strtoupper($toCurrency);
        $fromCurrency = strtoupper($fromCurrency);

        if ($fromCurrency === $toCurrency) {
            return round((float) $amount, 2);
        }

        $rates = $this->getRates();
        $baseAmount = (float) $amount;

        if ($fromCurrency !== $this->baseCurrency) {
            $baseAmount = $baseAmount / (float) ($rates[$fromCurrency] ?? 1);
        }

        return round($baseAmount * (float) ($rates[$toCurrency] ?? 1), 2);
    }

    public function format(float|int|string $amount, string $currency = 'USD', string $locale = 'en'): string
    {
        return Number::currency((float) $amount, strtoupper($currency), $locale);
    }

    public function refreshRates(): array
    {
        $rates = $this->getRates(forceRefresh: true);

        StorefrontCache::invalidateCurrencies();

        return $rates;
    }

    private function fetchRates(): array
    {
        if ($this->isPlaceholderAppId()) {
            return $this->fallbackRates();
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->get($this->baseUrl.'/latest.json', [
                'app_id' => $this->appId,
                'base' => $this->baseCurrency,
            ])
            ->throw()
            ->json('rates', []);

        return array_merge($this->fallbackRates(), $response);
    }

    private function cacheStore(): string
    {
        try {
            Cache::store('redis')->get('currency.redis_probe');

            return 'redis';
        } catch (Throwable) {
            return config('cache.default', 'file');
        }
    }

    private function isPlaceholderAppId(): bool
    {
        return $this->appId === '' || str_contains($this->appId, 'YOUR_');
    }

    private function fallbackRates(): array
    {
        return [
            'USD' => 1,
            'IDR' => 16000,
            'EUR' => 0.92,
            'GBP' => 0.79,
            'AUD' => 1.52,
            'SGD' => 1.34,
        ];
    }
}
