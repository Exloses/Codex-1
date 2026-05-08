# 📋 PROMPT TEMPLATE — Semua 30 Task untuk Codex
> Salin prompt yang sesuai dan kirim ke Codex satu per satu.
> JANGAN kirim semua sekaligus. Tunggu setiap task selesai 100%.

---

## ⚙️ PENGATURAN AWAL (Kirim SEKALI di awal sebelum Task 1)

```
Target repository: Exloses/Codex-1

You have access to my Windows computer and GitHub. Work autonomously as much as possible.

IMPORTANT: Read both AGENTS.md and BLUEPRINT_COMPLETE.md completely before doing anything.

Global rules (apply to ALL tasks):
- Environment: Windows localhost only. Do NOT deploy to production.
- Do NOT configure Oracle Cloud.
- Do NOT use real API keys or secrets. Use placeholders only.
- NEVER commit .env file. Only commit .env.example with placeholder values.
- NEVER push directly to main/master.
- NEVER merge PRs yourself. Owner will review and merge.
- Create a new branch for each task (see branch names in AGENTS.md).
- After each task: push branch, open a PR, and give full report.

These rules are permanent and apply to every single task.
```

---

## 📦 TASK 1 — Instalasi Project

```
Target repository: Exloses/Codex-1

Read AGENTS.md and BLUEPRINT_COMPLETE.md before starting.

Work on Task 1 only. Do NOT start Task 2.

Task 1 goal: Create and prepare the Laravel 11 + Vue 3 + Inertia.js + Tailwind CSS + Filament v3 project so it can run locally on Windows.

Before coding, check Windows for:
- Git, PHP 8.3+, Composer, Node.js LTS, npm, MySQL/MariaDB, Redis (optional)

If a tool is missing:
- Try to install using winget or official installer
- If UAC/admin is required, pause and tell me exactly what to approve
- If Redis is missing, use file/database driver instead — do NOT block Task 1

Implementation:
- Create Laravel project in repository root
- Install ALL Composer packages listed in Task 1 of BLUEPRINT_COMPLETE.md
- Install ALL NPM packages listed in Task 1
- Install Laravel Breeze with Vue + Inertia
- Install Filament v3
- Publish required configs
- Create .env.example with PLACEHOLDER values only (never real keys)
- Update README.md with complete Windows localhost instructions
- Update AGENTS.md if needed

Git: Branch = codex/task-1-laravel-installation → PR (do not merge)

Validation commands to run:
git --version, php -v, composer -V, node -v, npm -v,
composer validate, composer install, npm install,
php artisan about, php artisan route:list, npm run build

Report format: See AGENTS.md for full report template.
```

---

## 🗄️ TASK 2 — Database Migrations

```
Target repository: Exloses/Codex-1

Task 1 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 2 section.

Work on Task 2 only. Do NOT start Task 3.

Task 2 goal: Create all 34 database migrations listed in BLUEPRINT_COMPLETE.md Task 2.

Steps:
1. git checkout main && git pull origin main
2. git checkout -b codex/task-2-database-migrations
3. Create all migration files in the exact order listed in BLUEPRINT_COMPLETE.md
4. Make sure foreign key constraints are in correct order
5. Run: php artisan migrate
6. If MySQL is not running, give me instructions to start it

Validation:
- php artisan migrate --pretend (check for errors without running)
- php artisan migrate (actually run)
- php artisan db:show (verify all tables created)

Git: Branch = codex/task-2-database-migrations → PR (do not merge)

Report: List all 34 migration files created + migration result.
```

---

## 🔗 TASK 3 — Models & Relationships

```
Target repository: Exloses/Codex-1

Task 2 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 3 section.

Work on Task 3 only. Do NOT start Task 4.

Task 3 goal: Create all Eloquent Models with complete relationships.

Steps:
1. git checkout main && git pull origin main
2. git checkout -b codex/task-3-models-relationships
3. Create ALL models listed in BLUEPRINT_COMPLETE.md Task 3
4. Add all HasRoles trait (Spatie Permission) to User model
5. Add all relationships exactly as specified
6. Add casts where needed (json → array, etc.)
7. Add helper methods (isVendor(), isAdmin(), defaultAddress(), etc.)

Validation:
- php artisan tinker (test: User::first(), Product::with('vendor')->first())
- Verify no "Class not found" errors

Git: Branch = codex/task-3-models-relationships → PR (do not merge)

Report: List all model files created.
```

---

## ⚙️ TASK 4 — Services Layer

```
Target repository: Exloses/Codex-1

Task 3 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 4 section.

Work on Task 4 only. Do NOT start Task 5.

Task 4 goal: Create all Service classes in app/Services/.

Services to create:
- CurrencyService.php (Open Exchange Rates + Redis cache)
- StripeService.php (PaymentIntent + Webhook handler)
- PayPalService.php (Create + Capture order)
- EasyPostService.php (Get rates + Create label + Track)
- DropshipService.php (createDropshipOrders)
- AffiliateService.php (register + processCommission + checkAndUpgradeTier)
- LoyaltyService.php (earnPoints + redeemPoints + addBonusPoints)

IMPORTANT: Use PLACEHOLDER values for all API keys.
Register services in AppServiceProvider.

Validation:
- php artisan tinker (test: app(CurrencyService::class))
- Verify no syntax errors: php artisan about

Git: Branch = codex/task-4-services-layer → PR (do not merge)
```

---

## 🔄 TASK 5 — Background Jobs

```
Target repository: Exloses/Codex-1

Task 4 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 5 section.

Work on Task 5 only. Do NOT start Task 6.

Task 5 goal: Create all Queue Jobs.

Jobs to create:
- ProcessOrderAfterPayment.php (reduce stock, create dropship orders, process affiliate commission, earn loyalty points, clear cart, send confirmation email)
- SendEmailJob.php (with 3 retries and 60s backoff)
- UpdateCurrencyRatesJob.php

If Redis is not available on Windows, configure QUEUE_CONNECTION=database in .env.example and run:
php artisan queue:table && php artisan migrate

Validation:
- php artisan queue:work --once (test without actual jobs)
- php artisan about

Git: Branch = codex/task-5-background-jobs → PR (do not merge)
```

---

## ⏰ TASK 6 — Schedulers & Commands

```
Target repository: Exloses/Codex-1

Task 5 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 6 section.

Work on Task 6 only. Do NOT start Task 7.

Task 6 goal: Create all Artisan Commands and register Scheduler.

Commands to create:
- UpdateCurrencyRates (currency:update)
- ReleaseCommissions (affiliate:release-commissions)
- CancelUnpaidOrders (orders:cancel-unpaid)
- CheckStockNotifications (notifications:check-stock)
- CheckPriceDropAlerts (notifications:check-price-drops)
- CheckAffiliateTiers (affiliate:check-tiers)

Register all in routes/console.php scheduler.

Validation:
- php artisan list (verify all commands appear)
- php artisan currency:update --help
- php artisan schedule:list

Git: Branch = codex/task-6-schedulers-commands → PR (do not merge)
```

---

## 🛣️ TASK 7 — Routes

```
Target repository: Exloses/Codex-1

Task 6 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 7 section.

Work on Task 7 only. Do NOT start Task 8.

Task 7 goal: Create complete routes/web.php with all routes listed in BLUEPRINT_COMPLETE.md.

Include:
- Auth routes (login, register, forgot password, reset password)
- Social login routes (Google, Facebook)
- Storefront public routes
- Storefront authenticated routes
- Guest checkout route
- Vendor panel routes (with role middleware)
- Affiliate routes
- API routes (shipping, currency)
- Stripe webhook route (exclude CSRF!)

IMPORTANT: Stripe webhook MUST be excluded from CSRF middleware.
Create stub controllers (empty with placeholder return) for all controllers — full implementation is Task 8.

Validation:
- php artisan route:list
- Verify no duplicate route names
- Verify total routes count

Git: Branch = codex/task-7-routes → PR (do not merge)
```

---

## 🎮 TASK 8 — Controllers

```
Target repository: Exloses/Codex-1

Task 7 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 8 section.

Work on Task 8 only. Do NOT start Task 9.

Task 8 goal: Implement all controllers with full logic.

Controllers to implement (full list in BLUEPRINT_COMPLETE.md):
Auth/, Storefront/, Vendor/, AffiliateController, ShippingController, CurrencyController, SocialAuthController

Rules:
- Use Inertia::render() for page responses
- Use Form Request classes for validation
- Use Policies for authorization
- Dispatch Jobs for heavy processing
- NEVER expose vendor_price to storefront
- All prices stored in USD, convert in view only

Create all Form Request classes in app/Http/Requests/.
Create all Policy classes in app/Policies/.

Validation:
- php artisan route:list (no missing controllers)
- php artisan serve (app boots without error)
- Test homepage: curl http://localhost:8000

Git: Branch = codex/task-8-controllers → PR (do not merge)
```

---

## 👑 TASK 9 — Filament Admin Panel

```
Target repository: Exloses/Codex-1

Task 8 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 9 section.

Work on Task 9 only. Do NOT start Task 10.

Task 9 goal: Build complete Filament v3 admin panel.

Run all make:filament-resource and make:filament-widget commands listed in BLUEPRINT_COMPLETE.md Task 9.

Configure AdminPanelProvider with:
- Navigation groups
- Widgets (StatsOverview, RevenueChart, RecentOrders)
- Color theme: Blue

StatsOverview widget must show:
- Today Revenue (USD)
- Total Orders this month
- Active Vendors
- Pending Affiliate Payouts
- Open Support Tickets
- Pending Return Requests

Validation:
- php artisan serve
- Visit http://localhost:8000/admin
- Verify login page loads
- php artisan about

Git: Branch = codex/task-9-filament-admin → PR (do not merge)
```

---

## 🎨 TASK 10 — Vue Frontend

```
Target repository: Exloses/Codex-1

Task 9 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 10 section.

Work on Task 10 only. Do NOT start Task 11.

Task 10 goal: Create all Vue pages and components listed in BLUEPRINT_COMPLETE.md.

Priority order:
1. StorefrontLayout.vue (Navbar + Footer + Tawk.to placeholder)
2. VendorLayout.vue
3. Home.vue (homepage)
4. ProductShow.vue (with VariantSelector)
5. Cart.vue
6. Checkout.vue (multi-step)
7. All Account pages
8. All Vendor pages
9. All Auth pages
10. Affiliate pages

IMPORTANT:
- Use Inertia usePage() for shared data
- Use route() helper (Ziggy) for route generation
- Tailwind CSS for all styling
- All text in English (default language)
- Currency display: use formatCurrency() helper

Validation:
- npm run build (no errors)
- php artisan serve + visit http://localhost:8000
- Check homepage renders

Git: Branch = codex/task-10-vue-frontend → PR (do not merge)
```

---

## 🔒 TASK 11 — Security Middleware

```
Target repository: Exloses/Codex-1

Task 10 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 11 section.

Work on Task 11 only. Do NOT start Task 12.

Task 11 goal: Implement all security middleware.

Create:
- SecurityHeaders.php middleware
- SetUserCurrency.php middleware
- Rate limiters in AppServiceProvider (auth: 10/min, payment: 5/hour, api: 60/min)

Register all middleware in bootstrap/app.php.

Validation:
- php artisan serve
- curl -I http://localhost:8000 (check security headers)
- php artisan about

Git: Branch = codex/task-11-security-middleware → PR (do not merge)
```

---

## 📧 TASK 12 — Email Notifications

```
Target repository: Exloses/Codex-1

Task 11 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 12 section.

Work on Task 12 only. Do NOT start Task 13.

Task 12 goal: Create all Notification classes and email templates.

Run all make:notification commands listed in BLUEPRINT_COMPLETE.md Task 12.

Each notification must have:
- toMail() method with HTML email template
- toDatabase() method for in-app notifications

Create HTML email templates in resources/views/emails/:
- order-confirmation.blade.php
- order-shipped.blade.php (with tracking number)
- new-dropship-order.blade.php
- vendor-approved.blade.php
- affiliate-welcome.blade.php
- affiliate-commission-earned.blade.php
- affiliate-tier-upgrade.blade.php
- affiliate-payout-approved.blade.php
- affiliate-payout-paid.blade.php
- stock-available.blade.php
- price-drop.blade.php
- loyalty-points-earned.blade.php
- return-request-update.blade.php
- welcome.blade.php

Use MAIL_MAILER=log in .env for local testing (emails appear in storage/logs/laravel.log).

Validation:
- php artisan tinker: Notification::send() test
- Check storage/logs/laravel.log for email output

Git: Branch = codex/task-12-email-notifications → PR (do not merge)
```

---

## 🌱 TASK 13 — Database Seeders

```
Target repository: Exloses/Codex-1

Task 12 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 13 section.

Work on Task 13 only. Do NOT start Task 14.

Task 13 goal: Create all seeders with realistic demo data.

Seeders to create:
- RoleSeeder (admin, vendor, buyer roles)
- AdminSeeder (admin@platform.com / Admin123!)
- CategorySeeder (5 categories + size guides)
- ShippingZoneSeeder (3 zones + rates)
- BannerSeeder (3 homepage banners)
- FaqSeeder (20 FAQs, 5 per category)
- VendorSeeder (2 demo vendors)
- ProductSeeder (10 demo products with variants)

Demo credentials:
- Admin: admin@platform.com / Admin123!
- Vendor 1: vendor1@demo.com / Vendor123!
- Vendor 2: vendor2@demo.com / Vendor123!
- Buyer: buyer@demo.com / Buyer123!

Add to README.md.

Validation:
- php artisan db:seed
- php artisan tinker: User::count(), Product::count()
- Visit http://localhost:8000/admin with admin credentials

Git: Branch = codex/task-13-database-seeders → PR (do not merge)
```

---

## ⚡ TASK 14 — Performance Optimization

```
Target repository: Exloses/Codex-1

Task 13 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 14 section.

Work on Task 14 only. Do NOT start Task 15.

Task 14 goal: Implement caching and performance optimizations.

Steps:
1. Apply Cache::remember() in: StorefrontController, ProductController, CategoryController, CurrencyController, BannerController
2. Add cache invalidation in update/create/delete operations
3. Verify queue worker configuration
4. For Windows localhost: skip Redis config (use file driver)
5. Add database query optimization (eager loading, select specific columns)
6. Update vite.config.js for PWA preparation

Note: Do NOT run php artisan config:cache or route:cache on localhost — this breaks development.

Validation:
- php artisan serve
- Load homepage, check response time
- php artisan about

Git: Branch = codex/task-14-performance → PR (do not merge)
```

---

## ☁️ TASK 15 — Oracle Cloud Deployment Guide

```
Target repository: Exloses/Codex-1

Task 14 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 15 section.

Work on Task 15 only. Do NOT start Task 16.

Task 15 goal: Create deployment documentation and scripts for Oracle Cloud.

IMPORTANT: Do NOT actually deploy. Create documentation and scripts only.

Create:
- docs/DEPLOYMENT.md (step-by-step Oracle Cloud setup)
- deploy.sh (deployment script for Linux — do NOT run on Windows)
- nginx.conf (Nginx configuration)
- supervisor.conf (Queue worker configuration)
- .github/workflows/deploy.yml (optional CI/CD template — with placeholder secrets)

Update README.md with both:
- Windows localhost setup instructions
- Oracle Cloud production deployment reference

Git: Branch = codex/task-15-deploy-oracle → PR (do not merge)
```

---

## 🔐 TASK 16 — Social Login

```
Target repository: Exloses/Codex-1

Task 15 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 16 section.

Work on Task 16 only. Do NOT start Task 17.

Task 16 goal: Implement Google and Facebook social login.

Steps:
1. Add Google + Facebook config to config/services.php
2. Create SocialAuthController.php (redirect + callback methods)
3. Update Login.vue and Register.vue with social login buttons
4. Add routes to web.php
5. Add placeholder values to .env.example

For local testing, Google/Facebook OAuth needs real credentials.
Add note in README.md explaining how to get Google/Facebook OAuth credentials.

Validation:
- php artisan route:list | grep auth
- npm run build
- Visit http://localhost:8000/login (verify buttons appear)

Git: Branch = codex/task-16-social-login → PR (do not merge)
```

---

## 🛒 TASK 17 — Guest Checkout

```
Target repository: Exloses/Codex-1

Task 16 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 17 section.

Work on Task 17 only. Do NOT start Task 18.

Task 17 goal: Allow checkout without registration.

Steps:
1. Create migration to add guest_email + guest_name to orders table
2. Run: php artisan migrate
3. Update CheckoutController for guest handling
4. Update Checkout.vue with guest info form
5. Guest order confirmation via email (not account)
6. Guest can track order at /track-order with order_number + email

Validation:
- php artisan migrate
- php artisan serve
- Visit http://localhost:8000/checkout as non-logged-in user

Git: Branch = codex/task-17-guest-checkout → PR (do not merge)
```

---

## 💬 TASK 18 — Live Chat & Support Ticket

```
Target repository: Exloses/Codex-1

Task 17 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 18 section.

Work on Task 18 only. Do NOT start Task 19.

Task 18 goal: Integrate Tawk.to live chat and build support ticket system.

Tawk.to (use placeholder IDs):
- Add Tawk.to script to StorefrontLayout.vue using env variables
- Set user name/email via Tawk.to API if logged in

Support Ticket System:
- Migrations already in Task 2 (support_tickets, ticket_replies)
- Create SupportTicketController
- Create account page: Account/SupportTickets.vue
- Create Filament resource for admin
- Email notification on new ticket and reply

Validation:
- php artisan migrate (if any new migrations)
- npm run build
- php artisan route:list | grep support

Git: Branch = codex/task-18-livechat-support → PR (do not merge)
```

---

## ❤️ TASK 19 — Wishlist

```
Target repository: Exloses/Codex-1

Task 18 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 19 section.

Work on Task 19 only. Do NOT start Task 20.

Task 19 goal: Implement product wishlist feature.

Steps:
1. Create WishlistController (index, toggle)
2. Update ProductCard.vue — heart button with toggle
3. Create Account/Wishlist.vue page
4. Add "Move to Cart" button in wishlist
5. Add routes to web.php

Validation:
- npm run build
- php artisan route:list | grep wishlist
- Test toggle in browser

Git: Branch = codex/task-19-wishlist → PR (do not merge)
```

---

## 👟 TASK 20 — Product Variants (Color, Size, etc.)

```
Target repository: Exloses/Codex-1

Task 19 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 20 section.

Work on Task 20 only. Do NOT start Task 21.

Task 20 goal: Implement complete product variant system.

Steps:
1. Migrations already exist (product_attributes, product_attribute_values, product_variants)
2. Create VariantSelector.vue component:
   - Color: colored circles using color_hex
   - Size: text boxes
   - Out of stock: strikethrough + semi-transparent
3. Create SizeGuideModal.vue (table with US/EU/UK/CM columns)
4. Create ImageZoom.vue component
5. Update ProductShow.vue to use all new components
6. Update Vendor Panel product form to add attributes and variants

Validation:
- npm run build
- Visit product detail page — verify variant selector renders

Git: Branch = codex/task-20-product-variants → PR (do not merge)
```

---

## 📦 TASK 21 — Order Tracking

```
Target repository: Exloses/Codex-1

Task 20 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 21 section.

Work on Task 21 only. Do NOT start Task 22.

Task 21 goal: Real-time order tracking with timeline.

Steps:
1. Create TrackingController
2. Create Storefront/TrackOrder.vue with progress timeline
3. Guest tracking: form with order_number + email
4. Auth tracking: automatic from account
5. Add tracking link in shipped email template

Validation:
- npm run build
- Visit http://localhost:8000/track-order

Git: Branch = codex/task-21-order-tracking → PR (do not merge)
```

---

## 🔄 TASK 22 — Return & Refund

```
Target repository: Exloses/Codex-1

Task 21 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 22 section.

Work on Task 22 only. Do NOT start Task 23.

Task 22 goal: Complete return and refund system.

Steps:
1. Create ReturnController (store, show)
2. Create Account/Returns.vue
3. Create Filament resource for admin to manage returns
4. Add approve/reject + refund workflow
5. Email notifications for status updates

Validation:
- npm run build
- php artisan route:list | grep return

Git: Branch = codex/task-22-return-refund → PR (do not merge)
```

---

## 🏆 TASK 23 — Loyalty Points

```
Target repository: Exloses/Codex-1

Task 22 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 23 section.

Work on Task 23 only. Do NOT start Task 24.

Task 23 goal: Implement loyalty points reward system.

Rules:
- Earn: 10 points per $1 spent, 100 points on register, 50 points per review
- Redeem: 100 points = $1 discount (min 500 points to redeem)
- Points expire: 1 year after earning

Steps:
1. Update ProcessOrderAfterPayment job to call LoyaltyService::earnPoints()
2. Update Checkout.vue with "Redeem Points" option
3. Create Account/LoyaltyPoints.vue (balance + transaction history)
4. Trigger bonus points on register and review

Validation:
- npm run build
- php artisan tinker: test LoyaltyService

Git: Branch = codex/task-23-loyalty-points → PR (do not merge)
```

---

## 🔔 TASK 24 — Notification Center

```
Target repository: Exloses/Codex-1

Task 23 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 24 section.

Work on Task 24 only. Do NOT start Task 25.

Task 24 goal: In-app notification center.

Steps:
1. Run: php artisan notifications:table && php artisan migrate
2. Create NotificationController (index, markAsRead, markAllRead)
3. Create NotificationCenter.vue component (bell icon in Navbar)
4. Red badge for unread count
5. Polling every 30 seconds for new notifications

Validation:
- php artisan migrate
- npm run build
- Visit homepage — verify bell icon appears in navbar

Git: Branch = codex/task-24-notifications → PR (do not merge)
```

---

## 📬 TASK 25 — Newsletter

```
Target repository: Exloses/Codex-1

Task 24 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 25 section.

Work on Task 25 only. Do NOT start Task 26.

Task 25 goal: Newsletter subscription system.

Steps:
1. Create NewsletterController (subscribe + unsubscribe)
2. Add subscribe form in Footer (StorefrontLayout.vue)
3. Create Filament resource for admin to view subscribers
4. Send welcome email on subscribe (check storage/logs/laravel.log)

Validation:
- npm run build
- Check footer has newsletter form
- php artisan route:list | grep newsletter

Git: Branch = codex/task-25-newsletter → PR (do not merge)
```

---

## 🔕 TASK 26 — Stock & Price Alerts

```
Target repository: Exloses/Codex-1

Task 25 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 26 section.

Work on Task 26 only. Do NOT start Task 27.

Task 26 goal: Notify customers when out-of-stock items become available or price drops.

Steps:
1. Create StockNotificationController and PriceAlertController
2. Update ProductShow.vue:
   - "Notify Me" form when out of stock
   - "Alert me if price drops" button
3. Update Artisan commands (CheckStockNotifications, CheckPriceDropAlerts) with full logic
4. Send email notifications when triggered

Validation:
- npm run build
- php artisan notifications:check-stock (dry run)
- php artisan notifications:check-price-drops (dry run)

Git: Branch = codex/task-26-stock-alerts → PR (do not merge)
```

---

## ❓ TASK 27 — Product Q&A

```
Target repository: Exloses/Codex-1

Task 26 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 27 section.

Work on Task 27 only. Do NOT start Task 28.

Task 27 goal: Allow customers to ask questions on product pages.

Steps:
1. Create ProductQAController (store question, store answer)
2. Update ProductShow.vue — add Q&A tab
3. Vendor answers shown with "Vendor" badge
4. Email notification to vendor on new question

Validation:
- npm run build
- Visit product page — verify Q&A tab appears

Git: Branch = codex/task-27-product-qa → PR (do not merge)
```

---

## 🧾 TASK 28 — PDF Invoice

```
Target repository: Exloses/Codex-1

Task 27 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 28 section.

Work on Task 28 only. Do NOT start Task 29.

Task 28 goal: PDF invoice download for orders.

Steps:
1. Create InvoiceController with download() method using DomPDF
2. Create resources/views/invoices/order.blade.php (professional HTML template)
3. Invoice includes: logo, order number, items table, shipping address, totals
4. Add "Download Invoice" button in Account/OrderDetail.vue

Validation:
- npm run build
- php artisan tinker: test PDF generation
- Download test invoice

Git: Branch = codex/task-28-pdf-invoice → PR (do not merge)
```

---

## 📱 TASK 29 — PWA

```
Target repository: Exloses/Codex-1

Task 28 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 29 section.

Work on Task 29 only. Do NOT start Task 30.

Task 29 goal: Make the app installable as a Progressive Web App.

Steps:
1. Configure vite-plugin-pwa in vite.config.js
2. Create PWA icons: public/icons/icon-192.png and icon-512.png (placeholder)
3. Configure cache strategy for Cloudinary images
4. Add "Install App" prompt in Navbar (shows on mobile if not installed)
5. Test: npm run build — verify service worker is generated

Validation:
- npm run build
- Verify dist/sw.js and manifest.webmanifest are generated
- Open http://localhost:8000 in Chrome DevTools > Application > Manifest

Git: Branch = codex/task-29-pwa → PR (do not merge)
```

---

## ❓ TASK 30 — FAQ & Help Center

```
Target repository: Exloses/Codex-1

Task 29 PR has been merged. Pull latest main first.

Read AGENTS.md and BLUEPRINT_COMPLETE.md Task 30 section.

This is the FINAL task.

Task 30 goal: FAQ page and Help Center.

Steps:
1. Create FaqController (index with category filter)
2. Create Storefront/Faq.vue:
   - Category tabs: Shipping | Payment | Returns | Account | Products
   - Accordion per question
   - Search FAQ
   - "Was this helpful?" Yes/No button
3. Filament resource for admin to manage FAQs
4. FAQ seeder already done in Task 13 — verify 20 FAQs exist
5. Add FAQ link in Footer

Final validation (run all):
- npm run build
- php artisan about
- php artisan route:list
- php artisan db:show
- php artisan serve
- Visit all main pages and verify they load

Git: Branch = codex/task-30-faq-helpcenter → PR (do not merge)

🎉 PROJECT COMPLETE!
All 30 tasks finished. Open final PR and report full project summary.
```

---

## 🆘 PROMPT JIKA CODEX STUCK

```
Target repository: Exloses/Codex-1

Codex stopped working on Task [X]. Here is the last error/situation:
[paste error atau situasi di sini]

Please:
1. Diagnose what went wrong
2. Fix the issue
3. Continue Task [X] from where it stopped
4. Do NOT restart from Task 1
5. Do NOT skip any incomplete parts of Task [X]
```

---

## 🔁 PROMPT JIKA PERLU RE-DO TASK

```
Target repository: Exloses/Codex-1

Task [X] PR has issues. Please:
1. Check out branch: codex/task-X-nama-task
2. Fix the following issues: [deskripsi masalah]
3. Push the fixes to the same branch
4. Update the existing PR (do not create new PR)
```

---

*PROMPT_TEMPLATES.md — Panduan lengkap semua prompt untuk Codex*
*Repository: Exloses/Codex-1 | Total: 30 Tasks*
