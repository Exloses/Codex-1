<?php

namespace App\Services;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StorefrontCache
{
    public const HOME = 'storefront.home';
    public const CATEGORIES = 'storefront.categories';
    public const BANNERS = 'storefront.banners';

    public const HOME_TTL = 600;
    public const CATALOG_TTL = 900;
    public const PRODUCT_TTL = 900;
    public const FAQ_TTL = 1800;
    public const CURRENCY_TTL = 300;

    public static function remember(string $key, int $seconds, Closure $callback): mixed
    {
        return Cache::remember($key, $seconds, $callback);
    }

    public static function productIndexKey(Request $request): string
    {
        return 'products.index.'.self::version('products').'.'.self::hash([
            'q' => trim((string) $request->query('q', '')),
            'category' => trim((string) $request->query('category', '')),
            'sort' => trim((string) $request->query('sort', '')),
            'page' => max(1, (int) $request->query('page', 1)),
        ]);
    }

    public static function categoryProductsKey(string $slug, int $page): string
    {
        return 'storefront.category.'.self::version('categories').'.'.self::version('products').'.'.self::hash([
            'slug' => $slug,
            'page' => max(1, $page),
        ]);
    }

    public static function productShowKey(string $slug): string
    {
        return 'products.show.'.self::version('products').'.'.self::hash(['slug' => $slug]);
    }

    public static function faqKey(Request $request): string
    {
        return 'faqs.public.'.self::version('faqs').'.'.self::hash([
            'category' => trim((string) $request->query('category', '')),
            'language' => trim((string) $request->query('language', '')),
        ]);
    }

    public static function currencyKey(float $amount, string $currency): string
    {
        return 'currencies.active.'.self::version('currencies').'.'.self::hash([
            'amount' => round($amount, 6),
            'currency' => strtoupper($currency),
        ]);
    }

    public static function invalidateProducts(?Product $product = null): void
    {
        if ($product?->slug) {
            Cache::forget(self::productShowKey($product->slug));
        }

        Cache::forget(self::HOME);
        self::bump('products');
    }

    public static function invalidateCategories(): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::CATEGORIES);
        self::bump('categories');
        self::bump('products');
    }

    public static function invalidateBanners(): void
    {
        Cache::forget(self::HOME);
        Cache::forget(self::BANNERS);
        self::bump('banners');
    }

    public static function invalidateFaqs(): void
    {
        self::bump('faqs');
    }

    public static function invalidateCurrencies(): void
    {
        self::bump('currencies');
    }

    private static function version(string $namespace): string
    {
        return (string) Cache::rememberForever(self::versionKey($namespace), fn () => '1');
    }

    private static function bump(string $namespace): void
    {
        $key = self::versionKey($namespace);

        Cache::forever($key, (string) (((int) Cache::get($key, 1)) + 1));
    }

    private static function versionKey(string $namespace): string
    {
        return 'storefront.version.'.$namespace;
    }

    private static function hash(array $parts): string
    {
        Arr::sortRecursive($parts);

        return substr(sha1(json_encode($parts, JSON_THROW_ON_ERROR)), 0, 16);
    }
}
