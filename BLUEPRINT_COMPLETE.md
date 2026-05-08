# 🌍 BLUEPRINT LENGKAP — Global Multi-Vendor Dropship Platform
> ⚠️ INSTRUKSI UNTUK CODEX: Baca seluruh dokumen ini dari awal hingga akhir sebelum mulai bekerja.
> Kerjakan SATU TASK dalam satu waktu. Selesaikan 100% sebelum lanjut ke task berikutnya.
> Jika ada error, perbaiki dulu sebelum lanjut.

---

## 📋 RINGKASAN PROYEK

Platform e-commerce dropship global dengan 3 panel:
1. **Storefront** — Toko untuk pembeli dari seluruh dunia
2. **Vendor Panel** — Dashboard untuk supplier lokal Indonesia
3. **Admin Panel** — Dashboard pengelola platform (Filament v3)

### Alur Sistem:
```
Pembeli Global → Pesan & Bayar di Storefront
                        ↓
              Sistem otomatis kirim order ke Vendor
                        ↓
              Vendor kemas & kirim ke pembeli global
                        ↓
              Platform ambil margin (Harga Jual USD - Harga Vendor IDR)
```

---

## 🧰 TECH STACK

| Komponen | Teknologi |
|----------|-----------|
| Framework Backend | Laravel 11 |
| Frontend | Vue 3 + Inertia.js |
| CSS | Tailwind CSS |
| Admin Panel | Filament v3 |
| Auth | Laravel Breeze + Socialite |
| Role & Permission | Spatie Laravel Permission |
| Upload Media | Spatie Media Library + Cloudinary |
| Payment | Stripe + PayPal |
| Shipping | EasyPost API |
| Multi-Currency | Open Exchange Rates API |
| Queue & Cache | Redis + Laravel Queue |
| Email | Resend (SMTP) |
| Live Chat | Tawk.to (free) |
| PDF | Laravel DomPDF |
| PWA | Vite Plugin PWA |
| SEO | Spatie Laravel Sitemap |

---

## 🔧 KONFIGURASI `.env`

```env
APP_NAME="GlobalDropship"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dropship_global
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_YOUR_API_KEY
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

STRIPE_KEY=pk_test_YOUR_KEY
STRIPE_SECRET=sk_test_YOUR_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_KEY

PAYPAL_CLIENT_ID=YOUR_CLIENT_ID
PAYPAL_CLIENT_SECRET=YOUR_CLIENT_SECRET
PAYPAL_MODE=sandbox

EASYPOST_API_KEY=EZTKxxxx
OPEN_EXCHANGE_APP_ID=YOUR_APP_ID

CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME

GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback

FACEBOOK_CLIENT_ID=YOUR_FACEBOOK_APP_ID
FACEBOOK_CLIENT_SECRET=YOUR_FACEBOOK_APP_SECRET
FACEBOOK_REDIRECT_URL=http://localhost:8000/auth/facebook/callback

VITE_TAWK_PROPERTY_ID=YOUR_TAWK_PROPERTY_ID
VITE_TAWK_WIDGET_ID=YOUR_TAWK_WIDGET_ID
```

---

## 📁 STRUKTUR FOLDER LENGKAP

```
dropship-platform/
├── app/
│   ├── Console/Commands/
│   │   ├── UpdateCurrencyRates.php
│   │   ├── ReleaseCommissions.php
│   │   ├── CancelUnpaidOrders.php
│   │   ├── CheckStockNotifications.php
│   │   └── CheckPriceDropAlerts.php
│   ├── Filament/
│   │   ├── Resources/             ← CRUD admin panel
│   │   ├── Pages/
│   │   └── Widgets/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthController.php
│   │   │   │   └── SocialAuthController.php
│   │   │   ├── Storefront/
│   │   │   │   ├── StorefrontController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── AccountController.php
│   │   │   │   ├── WishlistController.php
│   │   │   │   ├── TrackingController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── ReturnController.php
│   │   │   │   ├── InvoiceController.php
│   │   │   │   ├── NewsletterController.php
│   │   │   │   └── SupportTicketController.php
│   │   │   ├── Vendor/
│   │   │   │   ├── VendorDashboardController.php
│   │   │   │   ├── VendorProductController.php
│   │   │   │   ├── VendorOrderController.php
│   │   │   │   ├── VendorFinanceController.php
│   │   │   │   └── VendorSettingsController.php
│   │   │   ├── AffiliateController.php
│   │   │   ├── ShippingController.php
│   │   │   └── CurrencyController.php
│   │   ├── Middleware/
│   │   │   ├── SecurityHeaders.php
│   │   │   └── SetUserCurrency.php
│   │   └── Requests/              ← Form validation
│   ├── Jobs/
│   │   ├── ProcessOrderAfterPayment.php
│   │   ├── SendEmailJob.php
│   │   └── UpdateCurrencyRatesJob.php
│   ├── Models/
│   ├── Notifications/
│   ├── Policies/
│   └── Services/
│       ├── StripeService.php
│       ├── PayPalService.php
│       ├── EasyPostService.php
│       ├── CurrencyService.php
│       ├── DropshipService.php
│       └── AffiliateService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   └── Register.vue
│   │   │   ├── Storefront/
│   │   │   │   ├── Home.vue
│   │   │   │   ├── ProductIndex.vue
│   │   │   │   ├── ProductShow.vue
│   │   │   │   ├── Cart.vue
│   │   │   │   ├── Checkout.vue
│   │   │   │   ├── CheckoutSuccess.vue
│   │   │   │   └── TrackOrder.vue
│   │   │   ├── Account/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Orders.vue
│   │   │   │   ├── OrderDetail.vue
│   │   │   │   ├── Addresses.vue
│   │   │   │   ├── Wishlist.vue
│   │   │   │   └── LoyaltyPoints.vue
│   │   │   ├── Affiliate/
│   │   │   │   ├── Landing.vue
│   │   │   │   ├── Dashboard.vue
│   │   │   │   └── Withdraw.vue
│   │   │   └── Vendor/
│   │   │       ├── Dashboard.vue
│   │   │       ├── Products/Index.vue
│   │   │       ├── Products/Create.vue
│   │   │       ├── Products/Edit.vue
│   │   │       ├── Orders/Index.vue
│   │   │       ├── Finance/Index.vue
│   │   │       └── Settings/Index.vue
│   │   ├── Components/
│   │   │   ├── Storefront/
│   │   │   │   ├── Navbar.vue
│   │   │   │   ├── Footer.vue
│   │   │   │   ├── ProductCard.vue
│   │   │   │   ├── HeroBanner.vue
│   │   │   │   ├── CategoryCard.vue
│   │   │   │   ├── TrustBadges.vue
│   │   │   │   ├── FlashSaleSection.vue
│   │   │   │   ├── SearchBar.vue
│   │   │   │   ├── CurrencySelector.vue
│   │   │   │   ├── LanguageSelector.vue
│   │   │   │   ├── CartDrawer.vue
│   │   │   │   ├── ShippingEstimator.vue
│   │   │   │   ├── VariantSelector.vue
│   │   │   │   ├── SizeGuideModal.vue
│   │   │   │   ├── ImageZoom.vue
│   │   │   │   ├── NotificationCenter.vue
│   │   │   │   ├── WishlistButton.vue
│   │   │   │   └── LiveChatWidget.vue
│   │   │   └── Vendor/
│   │   │       ├── Sidebar.vue
│   │   │       └── StatsCard.vue
│   │   ├── Layouts/
│   │   │   ├── StorefrontLayout.vue
│   │   │   └── VendorLayout.vue
│   │   └── app.js
│   └── views/
│       ├── app.blade.php
│       ├── emails/
│       └── invoices/
└── routes/
    └── web.php
```

---

# ═══════════════════════════════════════
# BAGIAN 1 — FOUNDATION (Task 1-15)
# ═══════════════════════════════════════

---

## ✅ TASK 1 — Instalasi Project & Semua Package

```bash
# Buat project Laravel
composer create-project laravel/laravel dropship-platform
cd dropship-platform

# Install semua package sekaligus
composer require \
  laravel/breeze \
  laravel/socialite \
  inertiajs/inertia-laravel \
  tightenco/ziggy \
  filament/filament:"^3.0" \
  spatie/laravel-permission \
  spatie/laravel-medialibrary \
  spatie/laravel-activitylog \
  spatie/laravel-sitemap \
  laravel/cashier \
  florianv/laravel-swap \
  easypost/easypost-php \
  maatwebsite/excel \
  intervention/image \
  cloudinary-labs/cloudinary-laravel \
  barryvdh/laravel-dompdf

# Install Breeze dengan Vue + Inertia
php artisan breeze:install vue --ssr

# Install Filament
php artisan filament:install --panels

# Publish configs
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"

# Install NPM
npm install
npm install vite-plugin-pwa @headlessui/vue @heroicons/vue swiper

# Generate app key
php artisan key:generate
```

**Selesai jika:** `php artisan serve` berjalan tanpa error di http://localhost:8000

---

## ✅ TASK 2 — Semua Database Migrations

Buat dan jalankan migration untuk semua tabel berikut (urut):

**1. Update `create_users_table`:**
```php
$table->id();
$table->string('name');
$table->string('email')->unique();
$table->string('password');
$table->string('phone')->nullable();
$table->string('avatar')->nullable();
$table->string('country', 2)->default('US');
$table->string('currency', 3)->default('USD');
$table->string('language', 5)->default('en');
$table->boolean('is_active')->default(true);
$table->timestamp('email_verified_at')->nullable();
$table->rememberToken();
$table->timestamps();
```

**2. `create_vendors_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('store_name');
$table->string('slug')->unique();
$table->text('description')->nullable();
$table->string('logo')->nullable();
$table->string('banner')->nullable();
$table->string('province')->nullable();
$table->string('city')->nullable();
$table->string('bank_name')->nullable();
$table->string('bank_account')->nullable();
$table->string('bank_holder')->nullable();
$table->boolean('is_approved')->default(false);
$table->decimal('commission_rate', 5, 2)->default(0);
$table->decimal('balance_idr', 15, 2)->default(0);
$table->timestamps();
```

**3. `create_categories_table`:**
```php
$table->id();
$table->string('name');
$table->string('name_id')->nullable();
$table->string('slug')->unique();
$table->string('icon')->nullable();
$table->string('image')->nullable();
$table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
$table->boolean('is_active')->default(true);
$table->integer('sort_order')->default(0);
$table->timestamps();
```

**4. `create_size_guides_table`:**
```php
$table->id();
$table->foreignId('category_id')->constrained()->cascadeOnDelete();
$table->string('name');
$table->json('columns');    // ["US", "EU", "UK", "CM"]
$table->json('rows');       // [["6","36","4","23"],["7","37","5","24"]]
$table->text('notes')->nullable();
$table->timestamps();
```

**5. `create_products_table`:**
```php
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->foreignId('category_id')->constrained();
$table->string('name');
$table->string('name_id')->nullable();
$table->string('slug')->unique();
$table->text('description');
$table->text('description_id')->nullable();
$table->decimal('vendor_price', 15, 2);
$table->decimal('selling_price', 15, 2);
$table->decimal('compare_price', 15, 2)->nullable();
$table->integer('stock')->default(0);
$table->integer('weight')->default(0);
$table->string('sku')->unique()->nullable();
$table->foreignId('size_guide_id')->nullable()->constrained()->nullOnDelete();
$table->boolean('is_active')->default(true);
$table->boolean('is_featured')->default(false);
$table->integer('total_sales')->default(0);
$table->decimal('average_rating', 3, 2)->default(0);
$table->json('videos')->nullable();
$table->timestamps();
$table->index(['is_active', 'is_featured']);
$table->index(['vendor_id', 'is_active']);
$table->index(['category_id', 'is_active']);
$table->fullText(['name', 'description']);
```

**6. `create_product_attributes_table`:**
```php
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->string('name');        // "Color", "Size", "Material"
$table->integer('sort_order')->default(0);
$table->timestamps();
```

**7. `create_product_attribute_values_table`:**
```php
$table->id();
$table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
$table->string('value');           // "Red", "XL", "Cotton"
$table->string('color_hex')->nullable();
$table->integer('sort_order')->default(0);
```

**8. `create_product_variants_table`:**
```php
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->json('combination');       // {"Color":"Red","Size":"XL"}
$table->string('sku')->nullable();
$table->decimal('price', 15, 2)->nullable();
$table->decimal('vendor_price', 15, 2)->nullable();
$table->integer('stock')->default(0);
$table->string('image')->nullable();
$table->timestamps();
```

**9. `create_addresses_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('full_name');
$table->string('phone');
$table->string('address_line1');
$table->string('address_line2')->nullable();
$table->string('city');
$table->string('state')->nullable();
$table->string('postal_code');
$table->string('country', 2);
$table->boolean('is_default')->default(false);
$table->timestamps();
```

**10. `create_cart_items_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
$table->integer('quantity')->default(1);
$table->timestamps();
```

**11. `create_orders_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained();
$table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
$table->string('order_number')->unique();
$table->string('guest_email')->nullable();
$table->string('guest_name')->nullable();
$table->string('status')->default('pending');
$table->decimal('subtotal_usd', 15, 2);
$table->decimal('shipping_cost_usd', 15, 2)->default(0);
$table->decimal('discount_usd', 15, 2)->default(0);
$table->decimal('total_usd', 15, 2);
$table->string('buyer_currency', 3)->default('USD');
$table->decimal('exchange_rate', 15, 6)->default(1);
$table->decimal('total_buyer_currency', 15, 2);
$table->string('payment_status')->default('unpaid');
$table->string('payment_method')->nullable();
$table->string('stripe_payment_id')->nullable();
$table->string('paypal_order_id')->nullable();
$table->string('affiliate_code')->nullable();
$table->text('notes')->nullable();
$table->timestamps();
$table->index(['user_id', 'status']);
$table->index(['payment_status', 'created_at']);
```

**12. `create_order_items_table`:**
```php
$table->id();
$table->foreignId('order_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained();
$table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
$table->foreignId('vendor_id')->constrained();
$table->integer('quantity');
$table->decimal('price_usd', 15, 2);
$table->decimal('subtotal_usd', 15, 2);
$table->text('custom_note')->nullable();
$table->timestamps();
```

**13. `create_dropship_orders_table`:**
```php
$table->id();
$table->foreignId('order_id')->constrained()->cascadeOnDelete();
$table->foreignId('vendor_id')->constrained();
$table->string('dropship_number')->unique();
$table->string('status')->default('pending');
$table->decimal('vendor_total_idr', 15, 2);
$table->boolean('is_paid_to_vendor')->default(false);
$table->timestamp('paid_at')->nullable();
$table->string('tracking_number')->nullable();
$table->string('carrier')->nullable();
$table->string('shipping_label')->nullable();
$table->timestamp('shipped_at')->nullable();
$table->timestamp('delivered_at')->nullable();
$table->text('notes')->nullable();
$table->timestamps();
$table->index(['vendor_id', 'status']);
```

**14. `create_shipping_zones_table`:**
```php
$table->id();
$table->string('name');
$table->json('countries');
$table->boolean('is_active')->default(true);
$table->timestamps();
```

**15. `create_shipping_rates_table`:**
```php
$table->id();
$table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
$table->string('name');
$table->string('carrier');
$table->integer('min_weight')->default(0);
$table->integer('max_weight')->default(99999);
$table->decimal('price_usd', 10, 2);
$table->string('estimated_days');
$table->timestamps();
```

**16. `create_reviews_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->tinyInteger('rating');
$table->string('title')->nullable();
$table->text('comment')->nullable();
$table->json('images')->nullable();
$table->boolean('is_verified')->default(false);
$table->timestamps();
```

**17. `create_banners_table`:**
```php
$table->id();
$table->string('title');
$table->string('title_id')->nullable();
$table->string('image');
$table->string('link')->nullable();
$table->boolean('is_active')->default(true);
$table->integer('sort_order')->default(0);
$table->timestamps();
```

**18. `create_coupons_table`:**
```php
$table->id();
$table->string('code')->unique();
$table->string('type');
$table->decimal('value', 10, 2);
$table->decimal('min_order_usd', 10, 2)->nullable();
$table->integer('max_uses')->nullable();
$table->integer('used_count')->default(0);
$table->timestamp('expires_at')->nullable();
$table->boolean('is_active')->default(true);
$table->timestamps();
```

**19. `create_wishlists_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->timestamps();
$table->unique(['user_id', 'product_id']);
```

**20. `create_loyalty_points_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->integer('balance')->default(0);
$table->timestamps();
```

**21. `create_loyalty_transactions_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained();
$table->integer('points');
$table->string('type');       // earned|redeemed|expired|bonus
$table->string('description');
$table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
$table->timestamp('expires_at')->nullable();
$table->timestamps();
```

**22. `create_stock_notifications_table`:**
```php
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('guest_email')->nullable();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
$table->string('type')->default('stock');   // stock|price_drop
$table->decimal('target_price_usd', 15, 2)->nullable();
$table->boolean('is_notified')->default(false);
$table->timestamps();
```

**23. `create_return_requests_table`:**
```php
$table->id();
$table->foreignId('order_id')->constrained();
$table->foreignId('user_id')->constrained();
$table->string('return_number')->unique();
$table->string('reason');
$table->text('description');
$table->json('images')->nullable();
$table->string('status')->default('pending');
$table->string('refund_method')->nullable();
$table->decimal('refund_amount_usd', 15, 2)->nullable();
$table->text('admin_notes')->nullable();
$table->timestamp('resolved_at')->nullable();
$table->timestamps();
```

**24. `create_affiliates_table`:**
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('referral_code')->unique();
$table->string('referral_link')->unique();
$table->string('tier')->default('starter');
$table->decimal('commission_rate', 5, 2)->default(5.00);
$table->integer('total_clicks')->default(0);
$table->integer('total_referrals')->default(0);
$table->integer('total_sales')->default(0);
$table->decimal('total_earned_usd', 15, 2)->default(0);
$table->decimal('total_paid_usd', 15, 2)->default(0);
$table->boolean('is_active')->default(true);
$table->timestamps();
```

**25. `create_affiliate_clicks_table`:**
```php
$table->id();
$table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
$table->string('ip_address');
$table->string('user_agent')->nullable();
$table->string('country', 2)->nullable();
$table->string('referer')->nullable();
$table->boolean('is_unique')->default(true);
$table->boolean('converted')->default(false);
$table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
$table->timestamp('clicked_at');
$table->timestamps();
```

**26. `create_affiliate_commissions_table`:**
```php
$table->id();
$table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
$table->foreignId('order_id')->constrained();
$table->decimal('order_total_usd', 15, 2);
$table->decimal('commission_rate', 5, 2);
$table->decimal('commission_usd', 15, 2);
$table->string('status')->default('pending');
$table->timestamp('available_at');
$table->timestamp('paid_at')->nullable();
$table->foreignId('payout_id')->nullable();
$table->timestamps();
```

**27. `create_affiliate_payout_methods_table`:**
```php
$table->id();
$table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
$table->string('type');        // paypal|wise|bank
$table->string('paypal_email')->nullable();
$table->string('wise_email')->nullable();
$table->string('wise_currency')->nullable();
$table->string('bank_name')->nullable();
$table->string('bank_account')->nullable();
$table->string('bank_holder')->nullable();
$table->string('bank_country')->nullable();
$table->string('swift_code')->nullable();
$table->boolean('is_default')->default(false);
$table->boolean('is_verified')->default(false);
$table->timestamps();
```

**28. `create_affiliate_payouts_table`:**
```php
$table->id();
$table->foreignId('affiliate_id')->constrained();
$table->foreignId('payout_method_id')->constrained('affiliate_payout_methods');
$table->decimal('amount_usd', 15, 2);
$table->decimal('fee_usd', 15, 2)->default(0);
$table->decimal('net_amount_usd', 15, 2);
$table->string('status')->default('pending');
$table->string('payout_type');
$table->string('paypal_email')->nullable();
$table->string('wise_email')->nullable();
$table->string('bank_account')->nullable();
$table->string('transaction_ref')->nullable();
$table->timestamp('processed_at')->nullable();
$table->text('notes')->nullable();
$table->timestamps();
```

**29. `create_support_tickets_table`:**
```php
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('guest_email')->nullable();
$table->string('guest_name')->nullable();
$table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
$table->string('ticket_number')->unique();
$table->string('subject');
$table->text('message');
$table->string('status')->default('open');
$table->string('priority')->default('normal');
$table->timestamps();
```

**30. `create_ticket_replies_table`:**
```php
$table->id();
$table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->text('message');
$table->boolean('is_staff')->default(false);
$table->timestamps();
```

**31. `create_newsletter_subscribers_table`:**
```php
$table->id();
$table->string('email')->unique();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('status')->default('active');
$table->string('token')->unique();
$table->timestamps();
```

**32. `create_product_questions_table`:**
```php
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained();
$table->text('question');
$table->boolean('is_public')->default(true);
$table->timestamps();
```

**33. `create_product_answers_table`:**
```php
$table->id();
$table->foreignId('question_id')->constrained('product_questions')->cascadeOnDelete();
$table->foreignId('user_id')->constrained();
$table->text('answer');
$table->boolean('is_vendor')->default(false);
$table->boolean('is_verified')->default(false);
$table->integer('helpful_count')->default(0);
$table->timestamps();
```

**34. `create_faqs_table`:**
```php
$table->id();
$table->string('category');    // shipping|payment|returns|account|products
$table->string('question');
$table->text('answer');
$table->string('language')->default('en');
$table->integer('sort_order')->default(0);
$table->integer('helpful_count')->default(0);
$table->boolean('is_active')->default(true);
$table->timestamps();
```

**Setelah semua migration dibuat:**
```bash
php artisan migrate
```

---

## ✅ TASK 3 — Semua Models & Relationships

Buat semua Eloquent Models di `app/Models/` dengan relationships lengkap:

- `User` — HasRoles, hasOne(Vendor), hasMany(Order, Address, CartItem, Review, Wishlist), hasOne(Affiliate), hasOne(LoyaltyPoint)
- `Vendor` — belongsTo(User), hasMany(Product, DropshipOrder, Withdrawal)
- `Category` — hasMany(Product, children), belongsTo(parent), hasOne(SizeGuide)
- `SizeGuide` — belongsTo(Category), cast columns+rows sebagai array
- `Product` — belongsTo(Vendor, Category), hasMany(ProductAttribute, ProductVariant, OrderItem, Review, ProductQuestion), morphMany(Media), belongsTo(SizeGuide)
- `ProductAttribute` — belongsTo(Product), hasMany(ProductAttributeValue)
- `ProductAttributeValue` — belongsTo(ProductAttribute)
- `ProductVariant` — belongsTo(Product), cast combination sebagai array
- `Order` — belongsTo(User, Address), hasMany(OrderItem, DropshipOrder), hasOne(AffiliateCommission)
- `OrderItem` — belongsTo(Order, Product, Vendor, ProductVariant)
- `DropshipOrder` — belongsTo(Order, Vendor)
- `ShippingZone` — hasMany(ShippingRate), cast countries sebagai array
- `Affiliate` — belongsTo(User), hasMany(AffiliateClick, AffiliateCommission, AffiliatePayout, AffiliatePayoutMethod)
- `Wishlist` — belongsTo(User, Product)
- `LoyaltyPoint` — belongsTo(User), hasMany(LoyaltyTransaction)
- `ReturnRequest` — belongsTo(Order, User)
- `SupportTicket` — belongsTo(User, Order), hasMany(TicketReply)

**Selesai jika:** `php artisan tinker` bisa akses semua model tanpa error

---

## ✅ TASK 4 — Services Layer

Buat semua Service classes di `app/Services/`:

**`CurrencyService.php`** — Fetch rates dari Open Exchange Rates, cache 1 jam di Redis, konversi dan format harga

**`StripeService.php`** — createPaymentIntent, handleWebhook (verifikasi signature), handlePaymentSuccess, handlePaymentFailed

**`PayPalService.php`** — createOrder, captureOrder, handleWebhook

**`EasyPostService.php`** — getRates (dari Indonesia ke negara tujuan), createShippingLabel, getTracking

**`DropshipService.php`** — createDropshipOrders (group by vendor, hitung total IDR, notif email vendor)

**`AffiliateService.php`** — register (generate kode unik), processCommission (hitung + hold 7 hari), checkAndUpgradeTier (Starter→Silver→Gold→Platinum)

**`LoyaltyService.php`** — earnPoints (10 poin per $1), redeemPoints (100 poin = $1 diskon), addBonusPoints (register, review, birthday)

---

## ✅ TASK 5 — Background Jobs

Buat Jobs di `app/Jobs/`:

**`ProcessOrderAfterPayment.php`:**
1. Kurangi stok produk
2. Tambah total_sales produk
3. Buat dropship orders (via DropshipService)
4. Proses komisi affiliate (via AffiliateService)
5. Tambah loyalty points pembeli (10 poin per $1)
6. Hapus cart user
7. Kirim email konfirmasi ke pembeli

**`SendEmailJob.php`** — Queue email dengan retry 3x

**`UpdateCurrencyRatesJob.php`** — Refresh cache kurs

---

## ✅ TASK 6 — Artisan Commands & Scheduler

Buat Commands:
- `UpdateCurrencyRates` — Refresh kurs setiap 1 jam
- `ReleaseCommissions` — Ubah komisi PENDING → AVAILABLE setelah 7 hari
- `CancelUnpaidOrders` — Cancel order yang belum bayar > 24 jam
- `CheckStockNotifications` — Kirim notif ke user jika stok sudah tersedia
- `CheckPriceDropAlerts` — Kirim notif jika harga turun

Daftarkan di scheduler (Kernel.php):
```php
$schedule->command('currency:update')->hourly();
$schedule->command('affiliate:release-commissions')->hourly();
$schedule->command('orders:cancel-unpaid')->everyThirtyMinutes();
$schedule->command('notifications:check-stock')->everyThirtyMinutes();
$schedule->command('notifications:check-price-drops')->hourly();
$schedule->command('affiliate:check-tiers')->daily();
```

---

## ✅ TASK 7 — Routes (web.php) Lengkap

```php
// ===== AUTH =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Social Login
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook')->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook');

// ===== STOREFRONT PUBLIC =====
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/track-order', [TrackingController::class, 'index'])->name('track.index');
Route::post('/track-order', [TrackingController::class, 'track'])->name('track.order');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/affiliate', [AffiliateController::class, 'landing'])->name('affiliate.landing');
Route::get('/ref/{code}', [AffiliateController::class, 'track'])->name('affiliate.track');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Currency & Language preferences (cookie)
Route::post('/preferences/currency', [PreferenceController::class, 'setCurrency'])->name('preferences.currency');
Route::post('/preferences/language', [PreferenceController::class, 'setLanguage'])->name('preferences.language');

// Shipping & Currency API
Route::post('/api/shipping/rates', [ShippingController::class, 'getRates']);
Route::get('/api/currency/rates', [CurrencyController::class, 'getRates']);

// Stripe Webhook — WAJIB exclude CSRF
Route::post('/webhook/stripe', [PaymentController::class, 'stripeWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ===== STOREFRONT AUTHENTICATED =====
Route::middleware(['auth'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon']);
    Route::post('/checkout/redeem-points', [CheckoutController::class, 'redeemPoints']);

    // Payment
    Route::post('/payment/stripe/intent', [PaymentController::class, 'createStripeIntent']);
    Route::post('/payment/paypal/create', [PaymentController::class, 'createPayPalOrder']);
    Route::post('/payment/paypal/capture', [PaymentController::class, 'capturePayPalOrder']);

    // Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [AccountController::class, 'orderDetail'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');
        Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/loyalty-points', [LoyaltyController::class, 'index'])->name('loyalty');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    });

    // Wishlist
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Returns
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{return}', [ReturnController::class, 'show'])->name('returns.show');

    // Support Tickets
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');

    // Stock & Price Notifications
    Route::post('/notifications/stock', [StockNotificationController::class, 'store']);
    Route::post('/notifications/price-alert', [PriceAlertController::class, 'store']);

    // Product Q&A
    Route::post('/products/{product}/questions', [ProductQAController::class, 'store']);
    Route::post('/questions/{question}/answers', [ProductQAController::class, 'answer']);

    // Affiliate
    Route::prefix('affiliate')->name('affiliate.')->middleware('auth')->group(function () {
        Route::post('/register', [AffiliateController::class, 'register'])->name('register');
        Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
        Route::get('/commissions', [AffiliateController::class, 'commissions'])->name('commissions');
        Route::post('/payout-methods', [AffiliateController::class, 'storePayoutMethod']);
        Route::post('/payouts', [AffiliateController::class, 'requestPayout'])->name('payouts.store');
        Route::get('/payouts', [AffiliateController::class, 'payoutHistory'])->name('payouts.index');
        Route::post('/generate-link', [AffiliateController::class, 'generateLink']);
    });
});

// Guest Checkout (tidak perlu auth)
Route::post('/checkout/guest', [CheckoutController::class, 'guestStore'])->name('checkout.guest');

// ===== VENDOR PANEL =====
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', VendorProductController::class);
    Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{dropship}/confirm', [VendorOrderController::class, 'confirm'])->name('orders.confirm');
    Route::put('/orders/{dropship}/ship', [VendorOrderController::class, 'markShipped'])->name('orders.ship');
    Route::get('/finance', [VendorFinanceController::class, 'index'])->name('finance');
    Route::post('/finance/withdraw', [VendorFinanceController::class, 'requestWithdrawal'])->name('finance.withdraw');
    Route::get('/settings', [VendorSettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [VendorSettingsController::class, 'update'])->name('settings.update');
});

// Vendor Registration (user biasa bisa apply)
Route::post('/vendor/register', [VendorDashboardController::class, 'registerVendor'])
    ->middleware('auth')->name('vendor.apply');
```

---

## ✅ TASK 8 — Semua Controllers

Buat semua controllers sesuai daftar di struktur folder. Setiap controller harus:
- Return `Inertia::render('PageName', [...data])` untuk halaman
- Return `response()->json([...])` untuk AJAX/API
- Gunakan Form Request untuk validasi input
- Gunakan Policy untuk authorization
- Dispatch Jobs untuk proses berat

---

## ✅ TASK 9 — Filament Admin Panel

```bash
php artisan make:filament-resource User --generate
php artisan make:filament-resource Vendor --generate
php artisan make:filament-resource Product --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource DropshipOrder --generate
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Banner --generate
php artisan make:filament-resource ShippingZone --generate
php artisan make:filament-resource Coupon --generate
php artisan make:filament-resource Affiliate --generate
php artisan make:filament-resource AffiliatePayout --generate
php artisan make:filament-resource ReturnRequest --generate
php artisan make:filament-resource SupportTicket --generate
php artisan make:filament-resource Faq --generate
php artisan make:filament-resource NewsletterSubscriber --generate
php artisan make:filament-widget StatsOverview --stats-overview
php artisan make:filament-widget RevenueChart --chart
php artisan make:filament-widget OrdersChart --chart
php artisan make:filament-widget RecentOrders --table
php artisan make:filament-widget TopAffiliates --table
```

**Dashboard Stats Widget** harus tampilkan:
- Revenue hari ini (USD)
- Total order bulan ini
- Vendor aktif
- Pending payouts affiliate
- Open support tickets
- Pending return requests

---

## ✅ TASK 10 — Vue Frontend (Semua Pages & Components)

Buat semua Vue pages dan components sesuai daftar di struktur folder.

**Prioritas utama:**
1. `StorefrontLayout.vue` — Dengan Navbar (social login buttons, currency selector, language selector, cart icon, notification bell, wishlist icon), Footer (newsletter subscribe, payment logos, carrier logos), Live Chat widget (Tawk.to), PWA install prompt
2. `Home.vue` — Hero banner (Swiper), trust badges, kategori grid, flash sale, featured products, newsletter section
3. `ProductShow.vue` — Image gallery dengan zoom, variant selector (color swatches + size boxes), size guide modal, stock info, add to cart, wishlist button, product Q&A tab, reviews tab, shipping estimator, social share buttons, "notify me" jika out of stock, price drop alert button
4. `Cart.vue` — Item list, quantity update, custom note per item, coupon input, loyalty points redeem, order summary
5. `Checkout.vue` — Step 1 (alamat + guest info), Step 2 (shipping options), Step 3 (payment: Stripe Elements + PayPal button)
6. `TrackOrder.vue` — Search form untuk guest + timeline tracking

---

## ✅ TASK 11 — Security Middleware

Buat dan daftarkan di `bootstrap/app.php`:

**`SecurityHeaders.php`** — Set X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy

**`SetUserCurrency.php`** — Baca cookie currency & language, set ke app locale

**Rate Limiters di `AppServiceProvider.php`:**
```php
RateLimiter::for('auth', fn() => Limit::perMinute(10)->by(request()->ip()));
RateLimiter::for('payment', fn() => Limit::perHour(5)->by(auth()->id() ?? request()->ip()));
RateLimiter::for('api', fn() => Limit::perMinute(60)->by(request()->ip()));
```

---

## ✅ TASK 12 — Email Notifications

```bash
php artisan make:notification OrderConfirmationNotification
php artisan make:notification OrderShippedNotification
php artisan make:notification NewDropshipOrderNotification
php artisan make:notification VendorApprovedNotification
php artisan make:notification AffiliateWelcomeNotification
php artisan make:notification AffiliateCommissionEarnedNotification
php artisan make:notification AffiliateTierUpgradeNotification
php artisan make:notification AffiliatePayoutApprovedNotification
php artisan make:notification AffiliatePayoutPaidNotification
php artisan make:notification StockAvailableNotification
php artisan make:notification PriceDropNotification
php artisan make:notification LoyaltyPointsEarnedNotification
php artisan make:notification ReturnRequestUpdateNotification
php artisan make:notification WelcomeNotification
```

Setiap notification implements `toMail()` + `toDatabase()`. Buat template HTML responsif di `resources/views/emails/`.

---

## ✅ TASK 13 — Database Seeders

```bash
php artisan make:seeder RoleSeeder          # Buat roles: admin, vendor, buyer
php artisan make:seeder AdminSeeder         # admin@platform.com / Admin123!
php artisan make:seeder CategorySeeder      # 5 kategori + size guides
php artisan make:seeder ShippingZoneSeeder  # 3 zona + rates
php artisan make:seeder BannerSeeder        # 3 banner homepage
php artisan make:seeder FaqSeeder           # 20 FAQ (5 per kategori)
php artisan make:seeder VendorSeeder        # 2 vendor demo
php artisan make:seeder ProductSeeder       # 10 produk demo dengan variants
```

```bash
php artisan db:seed
```

---

## ✅ TASK 14 — Performance Optimization

1. Pastikan `CACHE_DRIVER=redis` dan `QUEUE_CONNECTION=redis`
2. Terapkan `Cache::remember()` di semua controller untuk data yang sering diakses
3. Invalidasi cache saat data berubah (di setiap update/create/delete)
4. Jalankan queue worker: `php artisan queue:work redis --tries=3`
5. Optimize untuk production:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
npm run build
```

---

## ✅ TASK 15 — Deploy ke Oracle Cloud

```bash
# Di Oracle Cloud Linux server:
sudo apt update && sudo apt upgrade -y
sudo apt install php8.3 php8.3-fpm php8.3-mysql php8.3-redis \
  php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-gd \
  php8.3-intl php8.3-bcmath -y

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
sudo apt install mysql-server redis-server nginx supervisor -y

# Clone & setup project
git clone YOUR_REPO_URL /var/www/dropship-platform
cd /var/www/dropship-platform
composer install --optimize-autoloader --no-dev
npm install && npm run build
cp .env.example .env
# Edit .env dengan konfigurasi production
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Nginx config:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/dropship-platform/public;
    index index.php;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    gzip on;
    gzip_types text/plain text/css application/json application/javascript;
}
```

**Supervisor untuk queue:**
```ini
[program:laravel-worker]
command=php /var/www/dropship-platform/artisan queue:work redis --tries=3
autostart=true
autorestart=true
numprocs=2
stdout_logfile=/var/www/dropship-platform/storage/logs/worker.log
```

---

# ═══════════════════════════════════════
# BAGIAN 2 — CUSTOMER EXPERIENCE (Task 16-30)
# ═══════════════════════════════════════

---

## ✅ TASK 16 — Social Login (Google & Facebook)

1. Install `laravel/socialite` (sudah di Task 1)
2. Tambahkan config Google & Facebook di `config/services.php`
3. Buat `SocialAuthController.php` dengan method `redirect()` dan `callback()`
4. Di callback: cari user by email, jika tidak ada buat baru (otomatis verified)
5. Assign role 'buyer' ke user baru
6. Update `Login.vue` dan `Register.vue` dengan tombol "Login with Google" dan "Login with Facebook"
7. Daftarkan Google & Facebook OAuth di Google Console dan Facebook Developer

---

## ✅ TASK 17 — Guest Checkout

1. Tambahkan kolom `guest_email` dan `guest_name` di orders table
2. Update `CheckoutController` untuk handle checkout tanpa auth
3. Update `Checkout.vue` — tampilkan form nama + email jika belum login
4. Setelah order, kirim email konfirmasi ke guest_email
5. Guest bisa track order via halaman `/track-order` dengan order_number + email

---

## ✅ TASK 18 — Live Chat & Support Ticket

**Live Chat (Tawk.to):**
1. Daftar gratis di tawk.to, dapatkan Property ID dan Widget ID
2. Tambahkan script Tawk.to di `StorefrontLayout.vue`
3. Set nama + email user yang sudah login ke Tawk.to API

**Support Ticket:**
1. Buat `SupportTicketController` dengan CRUD
2. Buat halaman `Account/SupportTickets.vue`
3. Tambahkan Filament resource untuk admin kelola tiket
4. Kirim email notifikasi saat ada reply dari admin

---

## ✅ TASK 19 — Wishlist

1. Buat `WishlistController` dengan method `index()` dan `toggle()`
2. Update `ProductCard.vue` — tambahkan tombol hati (♥) di pojok kanan atas
3. Tombol hati: merah jika sudah di wishlist, abu-abu jika belum
4. Buat halaman `Account/Wishlist.vue` — grid produk yang di-wishlist
5. Tombol "Move to Cart" di setiap item wishlist

---

## ✅ TASK 20 — Product Variants Lengkap (Warna, Ukuran, dll)

1. Migration sudah dibuat di Task 2 (product_attributes, product_attribute_values, product_variants)
2. Buat `VariantSelector.vue` component:
   - Color: tampilkan bulatan warna (pakai color_hex)
   - Size: tampilkan kotak teks
   - Opsi yang out of stock: tampilkan dengan garis coret + transparan
3. Update `ProductShow.vue` — gunakan VariantSelector
4. Update harga & stok saat variant dipilih (computed property)
5. Buat `SizeGuideModal.vue` — tabel ukuran interaktif dengan konversi US/EU/UK/CM
6. Update Vendor Panel — form tambah produk bisa tambah attributes & variants
7. Buat `ImageZoom.vue` — zoom gambar saat hover (mirip Amazon)

---

## ✅ TASK 21 — Order Tracking Real-time

1. Buat `TrackingController` dengan tracking via EasyPost API
2. Buat halaman `Storefront/TrackOrder.vue`:
   - Form search untuk guest (order_number + email)
   - Timeline progress: Ordered → Paid → Processing → Shipped → Delivered
   - Detail tracking events dari kurir (lokasi, waktu, status)
3. Tambahkan link "Track Order" di email order shipped
4. Tambahkan tombol "Track" di halaman Account/Orders

---

## ✅ TASK 22 — Return & Refund System

1. Buat `ReturnController` — customer ajukan return, upload foto
2. Tambahkan Filament resource untuk admin kelola return
3. Buat halaman `Account/Returns.vue`
4. Alur: Customer ajukan → Admin review → Approve/Reject → Proses refund (Stripe/PayPal API)
5. Kirim email notifikasi setiap perubahan status

---

## ✅ TASK 23 — Loyalty Points

1. Buat `LoyaltyService` — earnPoints, redeemPoints, addBonusPoints
2. Earn otomatis setelah order paid (via ProcessOrderAfterPayment job)
3. Bonus: 100 poin saat register, 50 poin saat tulis review
4. Update Checkout — tambahkan opsi "Redeem Points" (100 poin = $1)
5. Buat halaman `Account/LoyaltyPoints.vue` — saldo + riwayat transaksi poin

---

## ✅ TASK 24 — Notification Center

1. Jalankan `php artisan notifications:table && php artisan migrate`
2. Semua notifikasi sudah pakai `toDatabase()` — tersimpan otomatis
3. Buat `NotificationController` — index, markAsRead, markAllRead
4. Buat `NotificationCenter.vue` component — dropdown lonceng di Navbar
5. Badge merah di atas lonceng menunjukkan jumlah notif yang belum dibaca
6. Real-time update via polling setiap 30 detik (atau Laravel Echo jika ada Pusher)

---

## ✅ TASK 25 — Newsletter

1. Buat `NewsletterController` — subscribe (simpan email + generate token), unsubscribe (via token)
2. Tambahkan form subscribe di footer `StorefrontLayout.vue`
3. Tambahkan Filament resource untuk admin lihat subscribers dan kirim newsletter
4. Kirim email welcome saat subscribe

---

## ✅ TASK 26 — Stock Notification & Price Alert

1. Buat controllers untuk stock notification dan price alert
2. Update `ProductShow.vue`:
   - Jika out of stock: tampilkan form "Notify Me" (input email)
   - Tambahkan tombol "Alert me if price drops"
3. Artisan commands sudah dibuat di Task 6:
   - `CheckStockNotifications` — cek stok, kirim email jika tersedia
   - `CheckPriceDropAlerts` — cek harga, kirim email jika turun

---

## ✅ TASK 27 — Product Q&A

1. Buat `ProductQAController` — store question, store answer
2. Update `ProductShow.vue` — tambahkan tab "Questions & Answers"
3. Tampilkan pertanyaan publik + jawaban (jawaban vendor diberi badge "Vendor")
4. Form tanya di bawah list pertanyaan
5. Kirim notif email ke vendor saat ada pertanyaan baru

---

## ✅ TASK 28 — PDF Invoice Download

1. Buat `InvoiceController` dengan method `download(Order $order)`
2. Buat template `resources/views/invoices/order.blade.php`:
   - Logo platform, judul "INVOICE"
   - Info order: nomor, tanggal, status
   - Tabel item: nama produk, qty, harga, subtotal
   - Alamat pengiriman
   - Total breakdown: subtotal, shipping, discount, total
   - Footer: thank you message + support email
3. Tambahkan tombol "Download Invoice" di Account/OrderDetail.vue

---

## ✅ TASK 29 — PWA (Progressive Web App)

1. Install `vite-plugin-pwa` (sudah di Task 1)
2. Konfigurasi di `vite.config.js`:
   - Nama app, ikon 192px dan 512px
   - Cache gambar Cloudinary (CacheFirst, 7 hari)
   - Cache halaman produk
3. Buat ikon PWA: `public/icons/icon-192.png` dan `icon-512.png`
4. Tambahkan "Install App" prompt di Navbar (tampil jika belum install)
5. Manfaat: Customer bisa install di HP tanpa app store, loading lebih cepat

---

## ✅ TASK 30 — FAQ & Help Center

1. Buat `FaqController` — index (filter by category + language)
2. Buat halaman `Storefront/Faq.vue`:
   - Tab kategori: Shipping | Payment | Returns | Account | Products
   - Accordion per pertanyaan (klik untuk expand jawaban)
   - Search FAQ
   - Tombol "Was this helpful? Yes/No" per jawaban
3. Tambahkan Filament resource untuk admin CRUD FAQ
4. Seed 20 FAQ default (sudah ada di Task 13)
5. Tambahkan link FAQ di footer storefront

---

# ═══════════════════════════════════════
# RINGKASAN FITUR LENGKAP
# ═══════════════════════════════════════

```
CUSTOMER:
✅ Register + Login (Email & Password)
✅ Social Login (Google + Facebook)
✅ Guest Checkout (beli tanpa daftar)
✅ Live Chat real-time (Tawk.to gratis)
✅ Support Ticket System
✅ Wishlist / Simpan produk favorit
✅ Pilih warna/ukuran (visual color swatch + size box)
✅ Size Guide / Size Chart per kategori
✅ Image Zoom saat hover
✅ Order Tracking real-time dengan timeline
✅ Return & Refund System
✅ Loyalty Points (10 poin per $1 belanja)
✅ Notification Center (lonceng navbar)
✅ Stock Notification ("Notify Me" saat out of stock)
✅ Price Drop Alert
✅ Product Q&A (tanya sebelum beli)
✅ Download Invoice PDF
✅ Newsletter subscription
✅ PWA (install di HP seperti app)
✅ FAQ & Help Center
✅ Multi-currency (USD, EUR, GBP, AUD, dll)
✅ Multi-language (EN, ID, dll)
✅ Custom note per item di cart

VENDOR:
✅ Dashboard dengan stats lengkap
✅ Kelola produk + multi-variant (warna, ukuran, material)
✅ Upload size guide
✅ Terima notif order baru
✅ Konfirmasi & update status order
✅ Input resi pengiriman
✅ Dashboard keuangan + request withdraw

ADMIN (Filament v3):
✅ Dashboard stats global (revenue, orders, vendors, dll)
✅ Kelola user, vendor, produk, kategori
✅ Approve/reject vendor
✅ Monitor semua pesanan + dropship orders
✅ Kelola affiliate + approve payouts
✅ Kelola return requests
✅ Kelola support tickets
✅ Kelola shipping zones + rates
✅ Kelola FAQ + Newsletter
✅ Kelola banners + coupons

MARKETING & GROWTH:
✅ Affiliate system (tier: Starter→Silver→Gold→Platinum)
✅ Flash Sale dengan countdown timer
✅ Coupon / voucher code
✅ Newsletter + promo email
✅ Loyalty points reward
✅ Social share button
✅ Price drop alert
✅ SEO (sitemap otomatis, meta tags dinamis)
✅ PWA push notification ready
```

---

## ⚠️ ATURAN PENTING UNTUK CODEX

1. **KERJAKAN URUT** Task 1 sampai Task 30
2. **SELESAIKAN 100%** sebelum lanjut ke task berikutnya
3. **JIKA ERROR** — perbaiki dulu, jangan lanjutkan
4. **VENDOR_PRICE RAHASIA** — jangan pernah tampilkan ke storefront/pembeli
5. **STRIPE WEBHOOK** harus exclude dari CSRF middleware
6. **HARGA DISIMPAN USD** di database, konversi hanya di view
7. **GUNAKAN JOBS** untuk proses berat (email, order processing)
8. **CACHE INVALIDATION** — hapus cache saat data berubah
9. **VALIDASI INPUT** menggunakan Form Request classes
10. **AUTHORIZATION** menggunakan Laravel Policies
11. **AFFILIATE** tidak bisa dapat komisi dari pembelian sendiri
12. **GUEST CHECKOUT** — simpan email guest di order, bukan di tabel users

---

## 🎯 CARA MEMBERIKAN TASK KE CODEX

**Pertama kali:**
```
Baca file BLUEPRINT_COMPLETE.md dari awal hingga akhir.
Kemudian kerjakan Task 1 sampai selesai 100%.
Jangan lanjut ke Task 2 sebelum Task 1 benar-benar selesai.
Setelah selesai, beritahu saya daftar file yang sudah dibuat.
```

**Untuk task selanjutnya:**
```
Task [X] sudah selesai. Sekarang kerjakan Task [X+1].
Selesaikan 100% dan beritahu saya hasilnya.
```

---

*BLUEPRINT LENGKAP — Laravel 11 + Vue 3 + Inertia.js + Filament v3*
*Global Multi-Vendor Dropship | Supplier Indonesia → Seluruh Dunia*
*Total: 30 Tasks | Versi: 2.0 FINAL*
