# PROJECT_MEMORY.md
> Persistent implementation notes for Codex. Keep this file updated at task boundaries and context checkpoints.

---

## Current Task

- Active task: Task 17 - Guest Checkout
- Last completed task: Task 16 - Social Login
- Branch: `codex/task-17-guest-checkout`
- Status: Task 17 completed locally; PR is being created.
- Pull request: pending
- Scope: Allow checkout without registration while preserving authenticated checkout.
- Task 16 PR #19 is merged into `main` via merge commit `37792ac`.
- Do not edit or commit `.env`, do not deploy, do not configure Oracle Cloud, and do not start Task 18.
- Next task after Task 17 is merged: Task 18 - Live Chat & Support.

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

- Task 1-16 are merged into `main`.
- Task 10 added Vue/Inertia storefront, account, vendor, affiliate, and auth page coverage.
- Task 11 added global security headers, web preference middleware, Inertia shared preference props, and named route throttles.
- Task 12 added email notifications and responsive Blade email templates using the mail and database notification channels.
- Task 13 added full demo seed data and credentials for local validation.
- Task 14 added local-safe storefront caching, model-driven invalidation, query optimization, database/file cache fallback, and Vite build chunk preparation.
- Task 16 added Google/Facebook Socialite login with placeholder-only OAuth configuration.

---

## Task 17 Completed Work

- Confirmed Task 16 PR #19 is merged into `main`, then created branch `codex/task-17-guest-checkout` from updated `main`.
- Added `database/migrations/2026_05_15_120000_enable_guest_checkout_orders.php`.
- Task 17 migration makes `orders.user_id` nullable and adds guest snapshot fields: `guest_phone`, `guest_address_line1`, `guest_address_line2`, `guest_city`, `guest_state`, `guest_postal_code`, and `guest_country`.
- Created `app/Services/GuestCartService.php` for session-backed guest carts without fake users.
- Updated `CartController` so guests can add/view/update/remove cart items through session, while authenticated users keep using `cart_items`.
- Moved cart routes and checkout display/success/coupon/guest routes outside the auth group; authenticated checkout store and payment routes remain protected.
- Implemented `CheckoutController::guestStore()` to create guest orders with `user_id = null`, guest contact/shipping fields, order items, totals, session/signed success access, guest cart clearing, and queued confirmation email through `SendEmailJob`.
- Updated `CheckoutController::success()` so guest orders require the creating session, a valid signed URL, or admin access.
- Updated tracking validation so `/track-order` requires order number plus email, preventing order lookup without an email.
- Updated Vue storefront pieces:
  - `Checkout.vue` now detects guest vs authenticated user, shows guest contact/shipping fields only for guests, posts to `checkout.guest` for guests, and displays validation errors.
  - `CheckoutSuccess.vue` shows guest tracking hints.
  - `TrackOrder.vue` requires email and shows a friendly not-found error.
  - `ProductCard.vue` add-to-cart button now posts to `cart.store`.
- Added `tests/Feature/GuestCheckoutTest.php` covering guest session cart, guest checkout persistence, guest cart clearing, guest tracking with correct/wrong email, authenticated checkout preservation, and `vendor_price` non-exposure in cart/checkout responses.
- No real payment credentials, OAuth credentials, API keys, or `.env` changes were used.

## Task 17 Validation

- Baseline `main` before branching:
  - `php artisan about`: passed via `E:\Codex\tools\php-8.3\php.exe`.
  - `php artisan route:list`: passed.
  - `php artisan test`: passed, 34 tests / 155 assertions.
  - `npm run build`: passed via `E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd`.
- Task 17 local validation:
  - `php artisan migrate`: passed.
  - `php -l` on Task 17 PHP files: passed.
  - `php artisan test --filter=GuestCheckoutTest`: passed, 4 tests / 24 assertions.
  - `php artisan about`: passed.
  - `php artisan route:list`: passed with 158 routes.
  - `php artisan test`: passed, 38 tests / 179 assertions.
  - `npm run build`: passed.
  - `php artisan serve --host=127.0.0.1 --port=8080`: started for HTTP smoke.
  - HTTP smoke: `/cart`, `/checkout`, `/track-order` all returned 200.
  - Browser plugin was not available through tool discovery in this session; HTTP/build validation was used as fallback.

---

## Task 15 Starting Notes

- Work is documentation/config sample only on Windows localhost.
- Oracle Cloud account/server is not available; all production deployment actions must remain documented commands, not executed remote actions.
- Use safe placeholders only in `.env.production.example`; never include a real `APP_KEY`, API key, token, password, SSH key, or secret.
- Deployment target examples should use `/var/www/dropship-platform`, `yourdomain.com`, PHP 8.3 FPM, Redis queues/cache/session, MySQL, Nginx, Supervisor, Git, Composer, and Node.js LTS.
- Required deliverables:
  - `docs/deployment/oracle-cloud.md`
  - `.env.production.example`
  - `docs/deployment/nginx-dropship-platform.conf`
  - `docs/deployment/supervisor-laravel-worker.conf`
  - `docs/deployment/deploy-checklist.md`
  - README deployment preparation reference
- Validation requested after documentation changes:
  - `php artisan about`
  - `php artisan route:list`
  - `php artisan test`
  - `npm run build`
  - verify `.env` is not staged/committed
  - verify docs/env examples use placeholders only

---

## Task 15 Completed Work

- Created `docs/deployment/oracle-cloud.md` as the main future Oracle Cloud deployment preparation guide.
- Created `.env.production.example` with safe placeholders only, including APP, DB, Redis, mail, Stripe, PayPal, EasyPost, Open Exchange, Cloudinary, Google/Facebook OAuth, and Tawk values.
- Created `docs/deployment/nginx-dropship-platform.conf` with placeholder `yourdomain.com`, Laravel public root, PHP 8.3 FPM socket, gzip, hidden file denial, and basic security headers.
- Created `docs/deployment/supervisor-laravel-worker.conf` for Redis queue workers with `www-data`, 2 processes, restart behavior, and storage log paths.
- Created `docs/deployment/deploy-checklist.md` as a manual future deployment checklist covering pre-deploy, provisioning, packages, PHP extensions, MySQL/Redis, app setup, env, frontend build, migrations, queue, scheduler, Nginx, SSL, smoke tests, and rollback.
- Updated `README.md` with Oracle Cloud Deployment Preparation references.
- Updated `PROJECT_STATUS.md` and `PROJECT_MEMORY.md`.
- No production deployment was performed.
- No Oracle Cloud login, SSH access, remote server command, or real credential handling was performed.
- Validation passed:
  - `php artisan about` via `E:\Codex\tools\php-8.3\php.exe` because `php` is not in PATH.
  - `php artisan route:list` with 158 routes.
  - `php artisan test` with 30 tests / 122 assertions.
  - `npm run build` via `E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd` because `npm` is not in PATH.
  - `.env` is not tracked by Git.
  - Placeholder/secret scan found no real secrets in Task 15 files.

---

## Next Task Gate

- Current task: Task 16 - Social Login.
- Task 15 PR #18 is merged, and post-merge validation on `main` passed on 2026-05-14:
  - `git checkout main`
  - `git pull origin main`
  - `php artisan test` via `E:\Codex\tools\php-8.3\php.exe`: 30 tests / 122 assertions passed.
  - `npm run build` via `E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd`: passed.
- Branch `codex/task-16-social-login` has been created from `main`.
- Task 16 OAuth credential handling must remain placeholder-only:
  - `GOOGLE_CLIENT_ID`
  - `GOOGLE_CLIENT_SECRET`
  - `GOOGLE_REDIRECT_URL`
  - `FACEBOOK_CLIENT_ID`
  - `FACEBOOK_CLIENT_SECRET`
  - `FACEBOOK_REDIRECT_URL`
- Task 16 local validation passed:
  - `php artisan about` via `E:\Codex\tools\php-8.3\php.exe`.
  - `php artisan route:list` with 158 routes.
  - `php artisan test` with 34 tests / 155 assertions.
  - `npm run build` via `E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd`.
  - `php artisan serve` on `http://127.0.0.1:8000`.
  - HTTP smoke `/login` and `/register` returned 200 and exposed Ziggy social routes.
  - Invalid provider `/auth/github/redirect` returned 302 to `/login`.
  - `/admin` redirected to `/admin/login`; `/vendor/dashboard` redirected to `/login`.
  - Browser plugin could not capture UI because no active Codex browser pane was available; fallback HTTP/build validation was used.
  - `.env` remains untracked and no real credential was found in Task 16 files.
- Next task: Task 17 - Guest Checkout, only after the Task 16 PR is merged by the owner.
- Task 16 draft PR: https://github.com/Exloses/Codex-1/pull/19

---

## Task 16 Completed Work

- Confirmed `laravel/socialite` already exists in `composer.json`; no reinstall was needed.
- Updated safe Facebook placeholder wording in `.env.example` and `config/services.php` to `YOUR_FACEBOOK_CLIENT_SECRET`.
- Implemented `app/Http/Controllers/Auth/SocialAuthController.php` with Google/Facebook provider allowlisting, Socialite redirect/callback, safe error redirects, email-based user lookup, new buyer defaults (`US`, `USD`, `en`, active), OAuth email verification, optional buyer role assignment if the role exists, `Auth::login()`, session regeneration, and intended dashboard redirect.
- Updated `routes/web.php` with named routes:
  - `social.redirect`: `GET /auth/{provider}/redirect`
  - `social.callback`: `GET /auth/{provider}/callback`
- Added Inertia shared `flash.status` and `flash.error` props in `HandleInertiaRequests`.
- Created `resources/js/Components/SocialAuthLinks.vue`.
- Updated `resources/js/Pages/Auth/Login.vue` and `resources/js/Pages/Auth/Register.vue` with clear `Continue with Google` and `Continue with Facebook` actions while preserving email/password auth.
- Added `tests/Feature/Auth/SocialAuthTest.php` using mocked Socialite only; no real Google/Facebook network call.
- Updated `README.md` with local Google/Facebook OAuth setup docs and redirect URLs:
  - `http://localhost:8000/auth/google/callback`
  - `http://localhost:8000/auth/facebook/callback`

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
