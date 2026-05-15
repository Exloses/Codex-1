# PROJECT_STATUS.md
> File ini diupdate otomatis oleh Codex setiap kali context window mendekati penuh (80-95%).
> Codex WAJIB membaca file ini sebelum melanjutkan kerja setelah compaction.
> Jangan hapus file ini.

---

## 🎯 1. TUJUAN UTAMA PROJECT

Platform e-commerce dropship global dengan 3 panel:
- **Storefront** — Toko untuk pembeli seluruh dunia
- **Vendor Panel** — Dashboard supplier lokal Indonesia
- **Admin Panel** — Dashboard pengelola platform (Filament v3)

**Stack:** Laravel 11 + Vue 3 + Inertia.js + Tailwind CSS + Filament v3
**Repository:** Exloses/Codex-1
**Environment:** Windows localhost
**Blueprint:** BLUEPRINT_COMPLETE.md (30 Tasks)

---

## ✅ 2. TASK YANG SUDAH SELESAI

<!-- Codex akan update bagian ini setiap kali task selesai -->

| Task | Nama | Status | Branch | PR |
|------|------|--------|--------|----|
| Task 1 | Laravel Installation | ✅ Selesai & PR merged | `codex/task-1-laravel-installation` | https://github.com/Exloses/Codex-1/pull/4 |
| Task 2 | Database Migrations | ✅ Selesai & PR merged | `codex/task-2-database-migrations` | https://github.com/Exloses/Codex-1/pull/5 |
| Task 3 | Models & Relationships | ✅ Selesai & PR merged | `codex/task-3-models-relationships` | https://github.com/Exloses/Codex-1/pull/6 |
| Task 4 | Services Layer | ✅ Selesai & PR merged | `codex/task-4-services-layer` | https://github.com/Exloses/Codex-1/pull/7 |
| Task 5 | Background Jobs | ✅ Selesai & PR merged | `codex/task-5-background-jobs` | https://github.com/Exloses/Codex-1/pull/8 |
| Task 6 | Schedulers & Commands | ✅ Selesai & PR merged | `codex/task-6-schedulers-commands` | https://github.com/Exloses/Codex-1/pull/9 |
| Task 7 | Routes | ✅ Selesai & PR merged | `codex/task-7-routes` | https://github.com/Exloses/Codex-1/pull/10 |
| Task 8 | Controllers | ✅ Selesai & PR merged | `codex/task-8-controllers` | https://github.com/Exloses/Codex-1/pull/11 |
| Task 9 | Filament Admin Panel | ✅ Selesai & PR merged | `codex/task-9-filament-admin` | https://github.com/Exloses/Codex-1/pull/12 |
| Task 10 | Vue Frontend | ✅ Selesai & PR merged | `codex/task-10-vue-frontend` | https://github.com/Exloses/Codex-1/pull/13 |
| Task 11 | Security Middleware | ✅ Selesai & PR merged | `codex/task-11-security-middleware` | https://github.com/Exloses/Codex-1/pull/14 |
| Task 12 | Email Notifications | ✅ Selesai & PR merged | `codex/task-12-email-notifications` | https://github.com/Exloses/Codex-1/pull/15 |
| Task 13 | Database Seeders | ✅ Selesai & PR merged | `codex/task-13-database-seeders` | https://github.com/Exloses/Codex-1/pull/16 |
| Task 14 | Performance Optimization | ✅ Selesai & PR merged | `codex/task-14-performance` | https://github.com/Exloses/Codex-1/pull/17 |
| Task 15 | Oracle Cloud Deployment | ✅ Selesai preparation-only & PR merged | `codex/task-15-deploy-oracle` | https://github.com/Exloses/Codex-1/pull/18 |
| Task 16 | Social Login | ✅ Selesai & PR merged | `codex/task-16-social-login` | https://github.com/Exloses/Codex-1/pull/19 |
| Task 17 | Guest Checkout | ✅ Selesai & PR merged | `codex/task-17-guest-checkout` | https://github.com/Exloses/Codex-1/pull/20 |
| Task 18 | Live Chat & Support | ✅ Selesai lokal; draft PR pending | `codex/task-18-livechat-support` | Pending |
| Task 19 | Wishlist | ⏳ Belum dimulai | - | - |
| Task 20 | Product Variants | ⏳ Belum dimulai | - | - |
| Task 21 | Order Tracking | ⏳ Belum dimulai | - | - |
| Task 22 | Return & Refund | ⏳ Belum dimulai | - | - |
| Task 23 | Loyalty Points | ⏳ Belum dimulai | - | - |
| Task 24 | Notification Center | ⏳ Belum dimulai | - | - |
| Task 25 | Newsletter | ⏳ Belum dimulai | - | - |
| Task 26 | Stock & Price Alerts | ⏳ Belum dimulai | - | - |
| Task 27 | Product Q&A | ⏳ Belum dimulai | - | - |
| Task 28 | PDF Invoice | ⏳ Belum dimulai | - | - |
| Task 29 | PWA | ⏳ Belum dimulai | - | - |
| Task 30 | FAQ & Help Center | ⏳ Belum dimulai | - | - |

**Status Legend:**
- ⏳ Belum dimulai
- 🔄 Sedang dikerjakan
- ✅ Selesai & PR merged
- ❌ Blocked / Ada masalah

---

## 📂 3. FILE YANG SUDAH DIBUAT / DIUBAH

**Task sedang dikerjakan:** Task 18 Live Chat & Support selesai lokal di branch `codex/task-18-livechat-support`; draft PR pending creation.

<!-- Codex update bagian ini setiap task selesai -->

```
Task 1:
- Laravel 11 project scaffold
- Breeze Vue/Inertia SSR authentication scaffolding
- Filament v3 admin panel installation
- Spatie Permission and Media Library configs/migrations
- Safe .env.example placeholders

Task 2:
- Updated users migration with profile/localization/status columns
- Added dropship platform migration with 33 domain tables
- Validated migrations with SQLite migrate:fresh on Windows localhost

Task 3:
- Created model files:
  app/Models/Address.php
  app/Models/Affiliate.php
  app/Models/AffiliateClick.php
  app/Models/AffiliateCommission.php
  app/Models/AffiliatePayout.php
  app/Models/AffiliatePayoutMethod.php
  app/Models/Banner.php
  app/Models/CartItem.php
  app/Models/Category.php
  app/Models/Coupon.php
  app/Models/DropshipOrder.php
  app/Models/Faq.php
  app/Models/LoyaltyPoint.php
  app/Models/LoyaltyTransaction.php
  app/Models/NewsletterSubscriber.php
  app/Models/Order.php
  app/Models/OrderItem.php
  app/Models/Product.php
  app/Models/ProductAnswer.php
  app/Models/ProductAttribute.php
  app/Models/ProductAttributeValue.php
  app/Models/ProductQuestion.php
  app/Models/ProductVariant.php
  app/Models/ReturnRequest.php
  app/Models/Review.php
  app/Models/ShippingRate.php
  app/Models/ShippingZone.php
  app/Models/SizeGuide.php
  app/Models/StockNotification.php
  app/Models/SupportTicket.php
  app/Models/TicketReply.php
  app/Models/Vendor.php
  app/Models/Wishlist.php
  app/Models/Withdrawal.php
- Modified files:
  app/Models/User.php
  PROJECT_STATUS.md
- Added Spatie HasRoles and helper methods to User.
- Added relationship methods, casts, guarded mass-assignment defaults, and Product media integration.
- Validated model loading with tinker relationship checks.

Task 4:
- Created service files:
  app/Services/CurrencyService.php
  app/Services/StripeService.php
  app/Services/PayPalService.php
  app/Services/EasyPostService.php
  app/Services/DropshipService.php
  app/Services/AffiliateService.php
  app/Services/LoyaltyService.php
- Modified files:
  app/Providers/AppServiceProvider.php
  config/services.php
  PROJECT_STATUS.md
- Registered all service classes in AppServiceProvider.
- Added placeholder-only service config for Open Exchange Rates, Stripe, PayPal, and EasyPost.
- Validated service container resolution and application boot.

Task 5:
- Created queue job files:
  app/Jobs/ProcessOrderAfterPayment.php
  app/Jobs/SendEmailJob.php
  app/Jobs/UpdateCurrencyRatesJob.php
- Modified files:
  .env.example
  PROJECT_STATUS.md
- ProcessOrderAfterPayment reduces stock, increments product sales, creates dropship orders, processes affiliate commission, earns loyalty points, clears user cart, and queues confirmation email.
- SendEmailJob uses 3 retries and 60s backoff.
- UpdateCurrencyRatesJob refreshes CurrencyService cache.
- Redis was not reachable on Windows localhost, so .env.example now uses QUEUE_CONNECTION=database.

Task 6:
- Created command files:
  app/Console/Commands/UpdateCurrencyRates.php
  app/Console/Commands/ReleaseCommissions.php
  app/Console/Commands/CancelUnpaidOrders.php
  app/Console/Commands/CheckStockNotifications.php
  app/Console/Commands/CheckPriceDropAlerts.php
  app/Console/Commands/CheckAffiliateTiers.php
- Modified files:
  bootstrap/app.php
  routes/console.php
  PROJECT_STATUS.md
- Registered command discovery with withCommands().
- Registered scheduler in routes/console.php for all six Task 6 commands.

Task 7:
- Replaced routes/web.php with complete Task 7 route map.
- Created placeholder controller stubs for all Task 7 controllers.
- Added shared placeholder response trait:
  app/Http/Controllers/Concerns/ReturnsPlaceholderResponses.php
- Created controller groups:
  app/Http/Controllers/Auth/AuthController.php
  app/Http/Controllers/Auth/SocialAuthController.php
  app/Http/Controllers/Storefront/*.php
  app/Http/Controllers/Vendor/*.php
  app/Http/Controllers/AffiliateController.php
  app/Http/Controllers/ShippingController.php
  app/Http/Controllers/CurrencyController.php
- Modified bootstrap/app.php to register Spatie role middleware alias and exclude webhook/stripe from CSRF validation.
- Stripe webhook route also uses route-level withoutMiddleware for Laravel CSRF middleware classes.

Task 8:
- Implemented all Task 8 controllers:
  app/Http/Controllers/Auth/AuthController.php
  app/Http/Controllers/Auth/SocialAuthController.php
  app/Http/Controllers/Storefront/StorefrontController.php
  app/Http/Controllers/Storefront/ProductController.php
  app/Http/Controllers/Storefront/CategoryController.php
  app/Http/Controllers/Storefront/CartController.php
  app/Http/Controllers/Storefront/CheckoutController.php
  app/Http/Controllers/Storefront/PaymentController.php
  app/Http/Controllers/Storefront/AccountController.php
  app/Http/Controllers/Storefront/WishlistController.php
  app/Http/Controllers/Storefront/TrackingController.php
  app/Http/Controllers/Storefront/ReviewController.php
  app/Http/Controllers/Storefront/ReturnController.php
  app/Http/Controllers/Storefront/InvoiceController.php
  app/Http/Controllers/Storefront/NewsletterController.php
  app/Http/Controllers/Storefront/SupportTicketController.php
  app/Http/Controllers/Storefront/FaqController.php
  app/Http/Controllers/Storefront/LoyaltyController.php
  app/Http/Controllers/Storefront/NotificationController.php
  app/Http/Controllers/Storefront/StockNotificationController.php
  app/Http/Controllers/Storefront/PriceAlertController.php
  app/Http/Controllers/Storefront/ProductQAController.php
  app/Http/Controllers/Vendor/VendorDashboardController.php
  app/Http/Controllers/Vendor/VendorProductController.php
  app/Http/Controllers/Vendor/VendorOrderController.php
  app/Http/Controllers/Vendor/VendorFinanceController.php
  app/Http/Controllers/Vendor/VendorSettingsController.php
  app/Http/Controllers/AffiliateController.php
  app/Http/Controllers/ShippingController.php
  app/Http/Controllers/CurrencyController.php
- Created Form Request classes:
  app/Http/Requests/AuthorizedRequest.php
  app/Http/Requests/Auth/ForgotPasswordRequest.php
  app/Http/Requests/Auth/RegisterRequest.php
  app/Http/Requests/Auth/ResetPasswordRequest.php
  app/Http/Requests/Api/ShippingRatesRequest.php
  app/Http/Requests/Storefront/*.php
  app/Http/Requests/Vendor/*.php
  app/Http/Requests/Affiliate/*.php
- Created Policy classes:
  app/Policies/AddressPolicy.php
  app/Policies/OrderPolicy.php
  app/Policies/ProductPolicy.php
  app/Policies/ReturnRequestPolicy.php
  app/Policies/SupportTicketPolicy.php
  app/Policies/VendorPolicy.php
- Modified config/services.php with placeholder-only Google/Facebook Socialite configuration.
- Modified resources/views/app.blade.php to load the Inertia app entry without requiring Task 10 Vue page chunks during server-side validation.
- Deleted the Task 7 placeholder response trait after replacing all placeholder controller actions.
- Storefront product responses explicitly avoid exposing vendor_price.

Task 9:
- Created Filament resources and CRUD pages for:
  app/Filament/Resources/UserResource.php
  app/Filament/Resources/VendorResource.php
  app/Filament/Resources/ProductResource.php
  app/Filament/Resources/OrderResource.php
  app/Filament/Resources/DropshipOrderResource.php
  app/Filament/Resources/CategoryResource.php
  app/Filament/Resources/BannerResource.php
  app/Filament/Resources/ShippingZoneResource.php
  app/Filament/Resources/CouponResource.php
  app/Filament/Resources/AffiliateResource.php
  app/Filament/Resources/AffiliatePayoutResource.php
  app/Filament/Resources/ReturnRequestResource.php
  app/Filament/Resources/SupportTicketResource.php
  app/Filament/Resources/FaqResource.php
  app/Filament/Resources/NewsletterSubscriberResource.php
- Created Filament dashboard widgets:
  app/Filament/Widgets/StatsOverview.php
  app/Filament/Widgets/RevenueChart.php
  app/Filament/Widgets/OrdersChart.php
  app/Filament/Widgets/RecentOrders.php
  app/Filament/Widgets/TopAffiliates.php
- Modified app/Providers/Filament/AdminPanelProvider.php to register the Task 9 dashboard widgets.
- Modified app/Models/User.php to restrict Filament admin panel access to active users with the admin role.
- Improved UserResource password handling and role assignment field.
- Improved ShippingZoneResource country input and nested shipping rate management.

Task 10:
- Created persistent project memory file:
  PROJECT_MEMORY.md
- Created reusable frontend pieces:
  resources/js/Layouts/StorefrontLayout.vue
  resources/js/Components/ProductCard.vue
  resources/js/Components/EmptyState.vue
  resources/js/Components/StatusBadge.vue
- Created Storefront pages:
  resources/js/Pages/Storefront/Home.vue
  resources/js/Pages/Storefront/ProductIndex.vue
  resources/js/Pages/Storefront/ProductShow.vue
  resources/js/Pages/Storefront/Cart.vue
  resources/js/Pages/Storefront/Checkout.vue
  resources/js/Pages/Storefront/TrackOrder.vue
  resources/js/Pages/Storefront/CheckoutSuccess.vue
  resources/js/Pages/Storefront/Faq.vue
- Created account/vendor/affiliate Inertia pages for all controller render targets:
  resources/js/Pages/Account/*.vue
  resources/js/Pages/Vendor/**/*.vue
  resources/js/Pages/Affiliate/*.vue
- Updated Auth/Login.vue and Auth/Register.vue with social login buttons and global buyer registration fields.
- Updated ResetPassword.vue and Profile password form to match the active route names.
- Restored Breeze-compatible dashboard, profile, email verification, and password confirmation routes in routes/web.php.
- Updated AuthController login/register redirects to use the dashboard route, which redirects authenticated users into their account panel.

Task 11:
- Created security middleware:
  app/Http/Middleware/SecurityHeaders.php
  app/Http/Middleware/SetUserCurrency.php
- Modified middleware/bootstrap files:
  bootstrap/app.php
  app/Http/Middleware/HandleInertiaRequests.php
- Modified rate limiter and route files:
  app/Providers/AppServiceProvider.php
  routes/web.php
- Updated storefront layout to consume shared currency preferences:
  resources/js/Layouts/StorefrontLayout.vue
- Added Task 11 test coverage:
  tests/Feature/SecurityMiddlewareTest.php
- Security headers now include X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy, and CSP allowlists for Stripe, PayPal, Tawk.to, and Cloudinary.
- Currency/language cookies are normalized into session and shared to all Inertia pages with availableCurrencies.
- Named rate limiters are attached to auth, payment, and API routes. Stripe webhook CSRF exclusion remains unchanged.

Task 12:
- Created notification classes:
  app/Notifications/GlobalDropshipNotification.php
  app/Notifications/OrderConfirmationNotification.php
  app/Notifications/OrderShippedNotification.php
  app/Notifications/NewDropshipOrderNotification.php
  app/Notifications/VendorApprovedNotification.php
  app/Notifications/AffiliateWelcomeNotification.php
  app/Notifications/AffiliateCommissionEarnedNotification.php
  app/Notifications/AffiliateTierUpgradeNotification.php
  app/Notifications/AffiliatePayoutApprovedNotification.php
  app/Notifications/AffiliatePayoutPaidNotification.php
  app/Notifications/StockAvailableNotification.php
  app/Notifications/PriceDropNotification.php
  app/Notifications/LoyaltyPointsEarnedNotification.php
  app/Notifications/ReturnRequestUpdateNotification.php
  app/Notifications/WelcomeNotification.php
- Created email Blade layout and templates:
  resources/views/emails/layout.blade.php
  resources/views/emails/order-confirmation.blade.php
  resources/views/emails/order-shipped.blade.php
  resources/views/emails/new-dropship-order.blade.php
  resources/views/emails/vendor-approved.blade.php
  resources/views/emails/affiliate-welcome.blade.php
  resources/views/emails/affiliate-commission-earned.blade.php
  resources/views/emails/affiliate-tier-upgrade.blade.php
  resources/views/emails/affiliate-payout-approved.blade.php
  resources/views/emails/affiliate-payout-paid.blade.php
  resources/views/emails/stock-available.blade.php
  resources/views/emails/price-drop.blade.php
  resources/views/emails/loyalty-points-earned.blade.php
  resources/views/emails/return-request-update.blade.php
  resources/views/emails/welcome.blade.php
- Created Laravel notifications table migration:
  database/migrations/2026_05_12_111521_create_notifications_table.php
- Modified .env.example to use MAIL_MAILER=log for local email testing.
- All Task 12 notifications deliver through mail and database channels and write database payloads.

Task 13:
- Created seeders:
  database/seeders/RoleSeeder.php
  database/seeders/AdminSeeder.php
  database/seeders/CategorySeeder.php
  database/seeders/ShippingZoneSeeder.php
  database/seeders/BannerSeeder.php
  database/seeders/FaqSeeder.php
  database/seeders/VendorSeeder.php
  database/seeders/ProductSeeder.php
- Modified database/seeders/DatabaseSeeder.php to call Task 13 seeders in order and create buyer demo users.
- Modified README.md with demo credentials.
- Seeded roles admin/vendor/buyer, admin demo, buyer demo, 5 categories with size guides, 3 shipping zones with rates, 3 banners, 20 FAQs, 2 approved vendors, 10 products, and 40 product variants.

Task 14:
- Created app/Services/StorefrontCache.php to centralize local-safe cache keys, TTLs, versioning, and invalidation without cache tags.
- Added cached storefront payloads for homepage, banners, categories, product listing filters/pages, category listing pages, product detail/related products, public FAQs, and currency API responses.
- Added cache invalidation on Product, ProductVariant, Category, Banner, Faq, Review, ProductQuestion, ProductAnswer, and SizeGuide model changes.
- Optimized storefront/account/vendor queries with eager loading and specific select columns where safe for existing Inertia payloads.
- Updated .env.example to use CACHE_STORE=database and SESSION_DRIVER=file for Windows localhost fallback; QUEUE_CONNECTION remains database.
- Updated vite.config.js with safe production build chunk splitting and dependency pre-bundling preparation, without enabling PWA.
- Added tests/Feature/StorefrontPerformanceTest.php for cached page rendering and cache version invalidation.

Task 15:
- Created docs/deployment/oracle-cloud.md with Oracle Cloud deployment preparation guide, production prerequisites, required software, Laravel setup steps, queue/scheduler/Nginx/SSL notes, backup/rollback strategy, and smoke test checklist.
- Created .env.production.example with safe placeholders only and no real APP_KEY or secrets.
- Created docs/deployment/nginx-dropship-platform.conf as a placeholder-domain Laravel Nginx sample for PHP 8.3 FPM.
- Created docs/deployment/supervisor-laravel-worker.conf as a Redis queue worker Supervisor sample.
- Created docs/deployment/deploy-checklist.md as a manual future deployment checklist.
- Updated README.md with Oracle Cloud Deployment Preparation references and credential safety warning.
- Updated PROJECT_STATUS.md and PROJECT_MEMORY.md with Task 15 preparation-only context.
- No production deployment was performed, no Oracle Cloud login was attempted, no SSH/server access was attempted, and no real credentials were used.

Task 16:
- Implemented Google/Facebook Socialite provider allowlisting, redirect, callback, friendly error handling, user creation, email verification, optional buyer role assignment, login, session regeneration, and dashboard intended redirect.
- Updated social auth routes to `/auth/{provider}/redirect` and `/auth/{provider}/callback` with route names `social.redirect` and `social.callback`.
- Added Inertia shared flash status/error props for callback errors.
- Updated Login/Register with `Continue with Google` and `Continue with Facebook` buttons while preserving email/password auth.
- Added `resources/js/Components/SocialAuthLinks.vue`.
- Added `tests/Feature/Auth/SocialAuthTest.php` with mocked Socialite coverage for invalid provider, redirect availability, new user callback, and existing unverified user callback.
- Updated `.env.example`, `config/services.php`, and README OAuth setup docs with safe placeholders only.
- No real Google/Facebook credential was used, no `.env` was edited, and no network OAuth credential test was performed.

Task 17:
- Created `database/migrations/2026_05_15_120000_enable_guest_checkout_orders.php` to make `orders.user_id` nullable and add guest shipping/contact snapshot fields.
- Created `app/Services/GuestCartService.php` for session-backed guest cart lines without creating fake users.
- Updated cart routes and `CartController` so guests can add, view, update, and remove cart items while authenticated users keep using `cart_items`.
- Updated checkout routes and `CheckoutController` so guests can place orders with `user_id = null`, `guest_email`, `guest_name`, shipping/contact fields, order items, totals, signed/session-protected success access, guest cart clearing, and queued confirmation email.
- Updated tracking request/controller flow so order tracking requires order number plus email and rejects wrong guest email.
- Updated `Checkout.vue`, `CheckoutSuccess.vue`, `TrackOrder.vue`, and `ProductCard.vue` for public cart/checkout, guest address form, validation errors, guest order hints, and add-to-cart actions.
- Added `tests/Feature/GuestCheckoutTest.php` covering guest cart checkout, guest order persistence, guest cart clearing, tracking with correct/wrong email, authenticated checkout preservation, and `vendor_price` non-exposure in cart/checkout responses.

Task 18:
- Created `app/Support/TawkSettings.php` for safe Tawk.to enablement checks and shared Inertia config.
- Updated `config/services.php`, `HandleInertiaRequests`, and `StorefrontLayout.vue` so Tawk.to loads only when non-placeholder env values are configured, stays disabled during tests, avoids duplicate script injection, handles load failure, and sets logged-in visitor name/email.
- Added `database/migrations/2026_05_15_180000_fix_ticket_replies_support_ticket_foreign_key.php` to correct `ticket_replies.ticket_id` so replies reference `support_tickets`.
- Updated `SupportTicket` model with constrained statuses/priorities and unique ticket number generation.
- Completed `SupportTicketController` index/create/store/show/reply flows with eager loading, buyer-owned order validation, authorization, status updates, and notifications.
- Added support ticket notifications and email templates for new tickets and replies.
- Added account support pages under `resources/js/Pages/Account/Support/` for list, create, and detail/reply workflows.
- Updated account/storefront navigation with support links.
- Upgraded Filament `SupportTicketResource` with searchable fields, status/priority filters, badges, latest-first sorting, editable status/priority, and admin staff reply action.
- Added `tests/Feature/SupportTicketTest.php` covering create, list/view authorization, reply authorization, buyer replies, ticket number generation, and Tawk disabled behavior.
- Updated README with Tawk.to/support setup notes.
```

---

## 🗄️ 4. STATUS DATABASE

<!-- Codex update setelah Task 2 selesai -->

```
Migrations: Berhasil dijalankan dengan php artisan migrate:fresh --seed
Seeders: Berhasil dijalankan untuk Task 13 demo data
Tables:
- Task 2 domain tables: users, vendors, categories, size_guides, products, product_attributes,
  product_attribute_values, product_variants, addresses, cart_items, orders, order_items,
  dropship_orders, shipping_zones, shipping_rates, reviews, banners, coupons, wishlists,
  loyalty_points, loyalty_transactions, stock_notifications, return_requests, affiliates,
  affiliate_clicks, affiliate_commissions, affiliate_payout_methods, affiliate_payouts,
  support_tickets, ticket_replies, newsletter_subscribers, product_questions, product_answers,
  faqs
- Package/framework tables also present: password_reset_tokens, sessions, cache, jobs,
  permissions/roles pivot tables, media, notifications
Seeded counts:
- users: 5
- vendors: 2
- categories: 5
- products: 10
- product_variants: 40
- faqs: 20
Task 17 migration:
- `2026_05_15_120000_enable_guest_checkout_orders` applied successfully on local SQLite.
- `orders.user_id` is nullable for guest orders.
- Guest snapshot fields added: guest_phone, guest_address_line1, guest_address_line2, guest_city, guest_state, guest_postal_code, guest_country.
Task 18 migration:
- `2026_05_15_180000_fix_ticket_replies_support_ticket_foreign_key` ensures `ticket_replies.ticket_id` references `support_tickets`.
Task 14 validation reseeded demo data with php artisan db:seed after tests left the local SQLite database empty.
```

---

## 💻 5. CARA MENJALANKAN DI LOCALHOST (WINDOWS)

```bash
# 1. Clone repository
git clone https://github.com/Exloses/Codex-1.git
cd Codex-1

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
# Pastikan MySQL sudah berjalan (Laragon/XAMPP)
# Buat database: dropship_global
php artisan migrate
php artisan db:seed

# 5. Jalankan server
php artisan serve
npm run dev

# 6. Buka browser
# Storefront: http://localhost:8000
# Admin Panel: http://localhost:8000/admin
# Login Admin: admin@platform.com / Admin123!
```

---

## 🔑 6. DEMO CREDENTIALS

```
Admin:    admin@platform.com   / Admin123!
Vendor 1: vendor1@demo.com     / Vendor123!
Vendor 2: vendor2@demo.com     / Vendor123!
Buyer:    buyer@demo.com       / Buyer123!
```

---

## ⚠️ 7. ERROR TERAKHIR / MASALAH YANG ADA

<!-- Codex update jika ada error -->

```
Tidak ada error saat ini.

Validasi terakhir post-merge Task 15 di main:
- PR #18 terkonfirmasi merged ke main pada 2026-05-14 12:19:11 UTC.
- git checkout main: berhasil.
- git pull origin main: berhasil fast-forward ke merge commit a16c592.
- php artisan test: berhasil via E:\Codex\tools\php-8.3\php.exe, 30 tests / 122 assertions.
- npm run build: berhasil via E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd.
- git ls-files .env: kosong, .env tidak tracked.
- Validasi Task 15 sebelumnya juga memastikan .env.production.example dan docs deployment hanya memakai placeholder aman.
- Tidak ada command remote/SSH/deploy yang dijalankan.
- Tidak ada perubahan aplikasi runtime selain dokumentasi dan contoh konfigurasi deployment.

Validasi Task 16 lokal:
- php artisan about: berhasil via E:\Codex\tools\php-8.3\php.exe.
- php artisan route:list: berhasil, 158 routes, termasuk social.redirect dan social.callback.
- php artisan test: berhasil, 34 tests / 155 assertions.
- npm run build: berhasil via E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd.
- php artisan serve: berhasil di http://127.0.0.1:8000.
- HTTP smoke /login: 200, Ziggy social route tersedia.
- HTTP smoke /register: 200, Ziggy social route tersedia.
- Invalid provider /auth/github/redirect: 302 ke /login dengan flash error.
- /admin: 302 ke /admin/login; /vendor/dashboard: 302 ke /login, route tidak rusak.
- Browser plugin tidak dapat dipakai untuk screenshot karena tidak ada active Codex browser pane di sesi ini; fallback validasi HTTP/build digunakan.
- `git ls-files .env`: kosong, `.env` tidak tracked.
- Secret scan Task 16 files: tidak menemukan credential asli.

Validasi Task 17 lokal:
- PR #19 terkonfirmasi merged ke main melalui merge commit `37792ac`.
- Baseline main sebelum branch: php artisan about berhasil, route:list berhasil, php artisan test berhasil 34 tests / 155 assertions, npm run build berhasil.
- Branch `codex/task-17-guest-checkout` dibuat dari main yang sudah memuat Task 16.
- php artisan migrate: berhasil, termasuk migration Task 17.
- php -l pada file PHP Task 17: berhasil.
- php artisan test --filter=GuestCheckoutTest: berhasil 4 tests / 24 assertions.
- php artisan about: berhasil via E:\Codex\tools\php-8.3\php.exe.
- php artisan route:list: berhasil, 158 routes.
- php artisan test: berhasil, 38 tests / 179 assertions.
- npm run build: berhasil via E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd.
- HTTP smoke dengan php artisan serve di http://127.0.0.1:8080: `/cart`, `/checkout`, dan `/track-order` semua 200.
- Browser plugin tidak tersedia lewat tool discovery pada sesi ini; fallback validasi HTTP/build digunakan.

Validasi Task 18 lokal:
- PR #20 Task 17 terkonfirmasi merged ke main; branch `codex/task-18-livechat-support` dibuat dari main terbaru.
- php artisan migrate: berhasil, Nothing to migrate setelah migration Task 18 tersedia/applied.
- php -l pada file PHP Task 18: berhasil.
- php artisan test --filter=SupportTicketTest: berhasil 6 tests / 42 assertions.
- php artisan about: berhasil via E:\Codex\tools\php-8.3\php.exe.
- php artisan route:list: berhasil, 162 routes.
- php artisan route:list --path=support: berhasil, 10 routes termasuk account support aliases dan Filament support resource.
- php artisan route:list --path=account/support: berhasil, 2 routes.
- php artisan test: berhasil, 44 tests / 221 assertions.
- npm run build: berhasil via E:\Codex\tools\node-v24.15.0-win-x64\npm.cmd untuk client dan SSR.
- HTTP smoke dengan php artisan serve di http://127.0.0.1:8080: `/`, `/cart`, `/checkout`, dan `/track-order` semua 200.
- HTTP smoke auth redirects: `/support` dan `/account/support` 302 ke `/login`; `/admin/support-tickets` 302 ke `/admin/login`.
```

---

## 🔧 8. TOOLS YANG TERSEDIA DI WINDOWS

<!-- Codex update setelah Task 1 selesai -->

```
Git:      Tersedia
PHP:      E:\Codex\tools\php-8.3\php.exe (8.3.31)
Composer: E:\Codex\tools\composer.phar (2.9.7)
Node.js:  E:\Codex\tools\node-v24.15.0-win-x64 (24.15.0)
npm:      11.12.1
MySQL:    Belum dicek
Redis:    Belum dicek
```

---

## ➡️ 9. LANGKAH BERIKUTNYA

<!-- Codex SELALU update bagian ini setelah setiap task -->

```
Task berikutnya: Task 19 - Wishlist, hanya setelah PR Task 18 merged oleh owner.
Branch yang akan dibuat nanti: codex/task-19-wishlist
Instruksi lengkap: Lihat BLUEPRINT_COMPLETE.md Task 19 dan PROMPT_TEMPLATES.md Task 19
Status: Task 18 selesai lokal di branch codex/task-18-livechat-support; draft PR pending creation.
Task 17 branch: codex/task-17-guest-checkout
Task 17 PR: https://github.com/Exloses/Codex-1/pull/20
Task 17 status: merged ke main.
Task 18 branch: codex/task-18-livechat-support
Task 18 PR: Pending
```

---

## 📝 10. CATATAN PENTING

<!-- Codex catat hal-hal penting yang perlu diingat -->

```
- Redis tidak tersedia/ tidak wajib di Windows → gunakan CACHE_STORE=database atau file, QUEUE_CONNECTION=database, SESSION_DRIVER=file.
- Task 14 cache keys: storefront.home, storefront.categories, storefront.banners, products.index.[version/hash], storefront.category.[version/hash], products.show.[version/hash], faqs.public.[version/hash], currencies.active.[version/hash].
- Task 14 invalidation tidak memakai cache tags agar aman untuk file/database/array cache drivers.
- Task 15 hanya deployment preparation. Jangan deploy production, jangan login Oracle Cloud, jangan SSH, dan jangan gunakan credential asli sampai owner menyiapkan akun/server.
- Task 15 deliverables: docs/deployment/oracle-cloud.md, docs/deployment/deploy-checklist.md, docs/deployment/nginx-dropship-platform.conf, docs/deployment/supervisor-laravel-worker.conf, .env.production.example, README.md update.
- Task 16 memakai placeholder OAuth aman saja. Jangan gunakan atau minta Google/Facebook OAuth credential asli, jangan edit `.env`, dan jangan melakukan network OAuth test yang butuh credential asli.
- Task 17 guest checkout memakai session cart untuk guest dan `cart_items` untuk user login; guest order menyimpan email/nama/alamat di tabel `orders`, bukan membuat user palsu.
- Guest success page dilindungi session atau signed URL sementara; tracking guest wajib order_number + email.
- Task 18 Tawk.to memakai placeholder `VITE_TAWK_PROPERTY_ID` dan `VITE_TAWK_WIDGET_ID`; script live chat tidak dimuat jika nilai kosong/placeholder atau saat automated tests.
- Task 18 support tickets hanya untuk user login; buyer hanya boleh melihat/reply tiket sendiri, admin dapat kelola status/prioritas dan reply dari Filament.
- Task 18 memperbaiki FK `ticket_replies.ticket_id` agar mengarah ke `support_tickets`.
- Queue worker lokal aman: php artisan queue:work --once. Gunakan redis worker hanya jika Redis benar-benar tersedia.
- Stripe Webhook HARUS exclude dari CSRF middleware
- vendor_price JANGAN PERNAH ditampilkan ke storefront
- Semua harga disimpan dalam USD di database
- Konversi mata uang hanya dilakukan di frontend/view
- Email lokal menggunakan MAIL_MAILER=log dan output muncul di storage/logs/laravel.log
- Task 12 notifications selalu mengirim via mail dan database channel
- Demo credentials tersedia di README.md untuk admin, vendor1, vendor2, dan buyer.
```

---

## 🕐 11. RIWAYAT UPDATE

<!-- Codex catat setiap kali file ini diupdate -->

| Tanggal | Update | Oleh |
|---------|--------|------|
| 2026-05-15 | Task 18 Live Chat & Support selesai lokal; validasi migrate, about, route:list, support tests, full test, dan npm build berhasil | Codex |
| 2026-05-15 | PR #20 Task 17 terkonfirmasi merged ke main; Task 18 dimulai di branch `codex/task-18-livechat-support` | Codex |
| 2026-05-15 | Task 17 draft PR #20 dibuat: https://github.com/Exloses/Codex-1/pull/20 | Codex |
| 2026-05-15 | Task 17 Guest Checkout selesai lokal; validasi migrate, about, route:list, test, npm build, dan HTTP smoke berhasil | Codex |
| 2026-05-15 | PR #19 Task 16 terkonfirmasi merged ke main; Task 17 dimulai di branch `codex/task-17-guest-checkout` | Codex |
| 2026-05-15 | Task 16 draft PR #19 dibuat: https://github.com/Exloses/Codex-1/pull/19 | Codex |
| 2026-05-15 | Task 16 Social Login selesai lokal; validasi php artisan about, route:list, test, npm build, dan smoke HTTP berhasil | Codex |
| 2026-05-15 | Task 16 Social Login dimulai di branch `codex/task-16-social-login`; catatan post-merge Task 15 dibawa ke branch Task 16 | Codex |
| 2026-05-14 | Post-merge sync main setelah PR #18; php artisan test dan npm run build berhasil | Codex |
| 2026-05-14 | Task 15 PR #18 dibuat; menunggu review/merge owner | Codex |
| 2026-05-14 | Task 15 Oracle Cloud Deployment Preparation selesai lokal; branch siap PR | Codex |
| 2026-05-14 | Task 15 Oracle Cloud Deployment Preparation dimulai setelah Task 14 merged ke main | Codex |
| 2026-05-14 | Task 14 Performance Optimization selesai lokal; branch siap PR | Codex |
| 2026-05-14 | Task 14 Performance Optimization dimulai setelah Task 13 merged ke main | Codex |
| 2026-05-12 | Task 13 Database Seeders selesai; PR #16 dibuat | Codex |
| 2026-05-12 | Task 13 Database Seeders dimulai setelah Task 12 merged ke main | Codex |
| 2026-05-12 | Task 12 Email Notifications selesai; PR #15 dibuat | Codex |
| 2026-05-12 | Task 12 Email Notifications dimulai setelah Task 11 merged ke main | Codex |
| 2026-05-12 | Task 11 Security Middleware selesai; PR #14 dibuat | Codex |
| 2026-05-12 | Task 11 Security Middleware dimulai setelah PR #13 merge ke main | Codex |
| 2026-05-11 | Task 10 Vue frontend selesai; Breeze route tests hijau kembali; PR #13 dibuat | Codex |
| 2026-05-11 | Task 10 dimulai setelah Task 9 merged; PROJECT_MEMORY.md dibuat | Codex |
| 2026-05-11 | Task 9 Filament admin resources dan dashboard widgets selesai, PR #12 dibuat | Codex |
| 2026-05-10 | Task 8 controllers, form requests, policies selesai dan siap PR | Codex |
| 2026-05-10 | Checkpoint context 78%: Task 1-6 merged, Task 7 PR open, tidak ada task aktif | Codex |
| 2026-05-10 | Task 7 routes dan stub controllers selesai, menunggu PR review | Codex |
| 2026-05-10 | Task 6 schedulers dan commands selesai, menunggu PR review | Codex |
| 2026-05-10 | Task 5 background jobs selesai, menunggu PR review | Codex |
| 2026-05-10 | Task 4 services layer selesai, menunggu PR review | Codex |
| 2026-05-10 | Context checkpoint: Task 1-2 merged, Task 3 PR open, tidak ada task aktif | Codex |
| 2026-05-10 | Task 3 models dan relationships selesai, menunggu PR review | Codex |
| 2026-05-08 | Task 2 migrations selesai dan tervalidasi lokal | Codex |
| 2026-05-08 | Task 1 ditandai selesai setelah PR merge | Codex |
| [DATE] | File dibuat pertama kali | Codex |

---

*File ini adalah "memori permanen" project.*
*Codex WAJIB update file ini setelah setiap task selesai.*
*Owner JANGAN edit file ini secara manual.*
