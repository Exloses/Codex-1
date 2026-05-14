# PROJECT_MEMORY.md
> Persistent implementation notes for Codex. Keep this file updated at task boundaries and context checkpoints.

---

## Current Task

- Active task: None
- Last completed task: Task 14 - Performance Optimization
- Branch: `codex/task-14-performance`
- Status: Completed locally; branch ready for PR.
- Pull request: Pending creation.
- Scope: Local-safe caching, invalidation, query optimization, queue readiness notes, and Vite build preparation.
- Do not start Task 15 until Task 14 PR is merged.

---

## Project Rules

- Never commit `.env` or real secrets.
- Never push directly to `main`.
- Never merge pull requests.
- Storefront must never expose `vendor_price`.
- Prices are stored in USD; frontend can display converted/selected currency labels.
- Stripe webhook CSRF exclusion must remain unchanged.
- Windows localhost is the target environment.

---

## Recent Baseline

- Task 1-13 are merged into `main`.
- Task 10 added Vue/Inertia storefront, account, vendor, affiliate, and auth page coverage.
- Task 11 added global security headers, web preference middleware, Inertia shared preference props, and named route throttles.
- Task 12 added email notifications and responsive Blade email templates using the mail and database notification channels.
- Task 13 added full demo seed data and credentials for local validation.
- Task 14 added local-safe storefront caching, model-driven invalidation, query optimization, database/file cache fallback, and Vite build chunk preparation.

---

## Task 14 Starting Notes

- Use `Cache::remember()` for public storefront data: homepage, categories, banners, product listing/detail, public FAQs, and active currencies.
- Use explicit `Cache::forget()` invalidation that works with file/database cache drivers; do not rely on cache tags.
- Keep Windows localhost safe: do not require Redis, do not deploy, and run `php artisan optimize:clear` after any temporary optimization validation.
- Queue worker validation should prefer `php artisan queue:work --once`; Redis-specific worker command is only appropriate if Redis is available.
- Preserve Task 10 Inertia payloads and keep `vendor_price` hidden from storefront responses.

---

## Task 14 Completed Work

- Created `app/Services/StorefrontCache.php` with explicit keys, TTLs, versioned dynamic cache keys, and invalidation helpers that work without cache tags.
- Cache keys added: `storefront.home`, `storefront.categories`, `storefront.banners`, `products.index.[version/hash]`, `storefront.category.[version/hash]`, `products.show.[version/hash]`, `faqs.public.[version/hash]`, and `currencies.active.[version/hash]`.
- Cached public storefront data in `StorefrontController`, `ProductController`, `CategoryController`, `FaqController`, and `CurrencyController`.
- Added model event invalidation for `Product`, `ProductVariant`, `Category`, `Banner`, `Faq`, `Review`, `ProductQuestion`, `ProductAnswer`, and `SizeGuide`.
- Optimized cart, checkout, account orders, product listing/detail, vendor products, vendor orders, vendor finance, and vendor dashboard queries with eager loading and safe select columns.
- Updated `.env.example` to default local cache/session fallback to `CACHE_STORE=database` and `SESSION_DRIVER=file`; `QUEUE_CONNECTION=database` remains the local-safe queue default.
- Updated `vite.config.js` with client chunk splitting and dependency pre-bundling preparation; no PWA feature was enabled.
- Added `tests/Feature/StorefrontPerformanceTest.php`.
- Validation passed:
  - `php artisan about`
  - `php artisan route:list`
  - `php artisan test` with 30 tests / 122 assertions
  - `npm run build`
  - `php artisan optimize:clear`
  - `php artisan queue:work --once`
  - Browser smoke for homepage, product listing, product detail, and unauthenticated cart/checkout redirects.
- Local validation used `CACHE_STORE=database`, `SESSION_DRIVER=file`, and `QUEUE_CONNECTION=database`; Redis was not required.

---

## Task 10 Completed Work

- Added `StorefrontLayout.vue` with nav, social login links, currency/language selectors, cart/wishlist/notification/account links, newsletter signup, payment/carrier badges, Tawk.to script hook, and PWA install prompt.
- Added storefront pages: Home, ProductIndex, ProductShow, Cart, Checkout, TrackOrder, CheckoutSuccess, and FAQ.
- Added account, vendor, and affiliate pages for every current Inertia render target.
- Updated auth pages with social login links and route-name fixes.
- Task 10 validation passed:
  - `npm run build`
  - `php artisan about`
  - `php artisan route:list`
  - `php artisan test` with 25 tests and 61 assertions
  - Browser render check for `http://127.0.0.1:8000` and `/track-order`
- Pull request: https://github.com/Exloses/Codex-1/pull/13

---

## Task 11 Completed Work

- Added `app/Http/Middleware/SecurityHeaders.php` with X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy, and a CSP allowlist for self, Stripe.js, PayPal, Tawk.to, and Cloudinary.
- Added `app/Http/Middleware/SetUserCurrency.php` to normalize `currency` and `language` cookies into session and set the app locale.
- Registered `SecurityHeaders` globally and `SetUserCurrency` in the web middleware stack before Inertia sharing.
- Shared `currency`, `language`, and `availableCurrencies` from `HandleInertiaRequests`.
- Registered named rate limiters: `auth` 10/min per IP, `payment` 5/hour per user ID or IP, `api` 60/min per IP.
- Applied throttles to requested auth, payment, and API routes.
- Updated `StorefrontLayout.vue` to use shared currency/language props and the shared available currency list.
- Added `tests/Feature/SecurityMiddlewareTest.php`.
- Task 11 validation passed:
  - `php -l` on changed PHP files
  - `php artisan about`
  - `php artisan route:list`
  - `php artisan route:list -v` filters for auth/payment/api throttles
  - `php artisan test` with 28 tests and 108 assertions
  - `npm run build`
  - `php artisan serve` on `http://127.0.0.1:8000`
  - `curl -I http://127.0.0.1:8000/` shows security headers
  - `curl -L http://127.0.0.1:8000/admin` returns 200 after admin login redirect

---

## Task 12 Completed Work

- Created `app/Notifications/GlobalDropshipNotification.php` for common mail payload, database payload, money formatting, order item, address, URL, and date helpers.
- Created all 14 requested notification classes:
  - order confirmation, shipped order, new dropship order, vendor approval
  - affiliate welcome, commission earned, tier upgrade, payout approved, payout paid
  - stock available, price drop, loyalty points earned, return request update, welcome
- Every notification class defines `via()`, `toMail()`, and `toDatabase()` and returns `['mail', 'database']` from `via()`.
- Created `resources/views/emails/layout.blade.php` with responsive max-width 600px blue/white branding, GlobalDropship header, unsubscribe link, support email from config, and company address.
- Created the 14 requested email templates under `resources/views/emails/`.
- Ran `php artisan notifications:table` and `php artisan migrate`; `notifications` table migration was created and applied.
- Updated `.env.example` to `MAIL_MAILER=log` for local testing; emails are written to `storage/logs/laravel.log`.
- Task 12 validation passed:
  - `php -l` for all notification PHP files
  - `php artisan about`
  - `php artisan migrate`
  - `php artisan test` with 28 tests and 108 assertions
  - `php artisan tinker` manual `WelcomeNotification` send after creating a local validation user because the database initially had no users
  - `storage/logs/laravel.log` confirmed `Welcome to GlobalDropship`
  - `npm run build`

---

## Task 13 Completed Work

- Created `RoleSeeder`, `AdminSeeder`, `CategorySeeder`, `ShippingZoneSeeder`, `BannerSeeder`, `FaqSeeder`, `VendorSeeder`, and `ProductSeeder`.
- Updated `DatabaseSeeder` to run seeders in the requested order and create demo buyer accounts.
- Seeded Spatie roles: `admin`, `vendor`, `buyer`.
- Seeded demo credentials:
  - admin: `admin@platform.com` / `Admin123!`
  - vendor 1: `vendor1@demo.com` / `Vendor123!`
  - vendor 2: `vendor2@demo.com` / `Vendor123!`
  - buyer: `buyer@demo.com` / `Buyer123!`
- Preserved an extra `test@example.com` buyer so the requested `User::count()` validation reaches at least 5 users.
- Seeded 5 active categories with size guides, 3 shipping zones with rates, 3 homepage banners, 20 realistic FAQs, 2 approved vendors, 10 demo products, and 40 variants.
- Product placeholder images are stored on variants because the current `products` table does not have an `image` column; product `videos` also stores the placeholder image URL metadata.
- Added a README demo credentials section.
- Task 13 validation passed:
  - `php -l database/seeders/*.php`
  - `php artisan migrate:fresh --seed`
  - `php artisan tinker` counts: users=5, products=10, categories=5, vendors=2
  - admin login to `/admin` with HTTP session, CSRF, and Laravel cookies
  - vendor login to `/vendor/dashboard` with HTTP session, CSRF, and Laravel cookies
  - `php artisan test` with 28 tests and 108 assertions
- Browser plugin was attempted for local login validation, but tab navigation timed out; HTTP session validation was used as fallback.
