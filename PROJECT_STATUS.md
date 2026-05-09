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
| Task 3 | Models & Relationships | ✅ Selesai, PR pending review | `codex/task-3-models-relationships` | https://github.com/Exloses/Codex-1/pull/6 |
| Task 4 | Services Layer | ⏳ Belum dimulai | - | - |
| Task 5 | Background Jobs | ⏳ Belum dimulai | - | - |
| Task 6 | Schedulers & Commands | ⏳ Belum dimulai | - | - |
| Task 7 | Routes | ⏳ Belum dimulai | - | - |
| Task 8 | Controllers | ⏳ Belum dimulai | - | - |
| Task 9 | Filament Admin Panel | ⏳ Belum dimulai | - | - |
| Task 10 | Vue Frontend | ⏳ Belum dimulai | - | - |
| Task 11 | Security Middleware | ⏳ Belum dimulai | - | - |
| Task 12 | Email Notifications | ⏳ Belum dimulai | - | - |
| Task 13 | Database Seeders | ⏳ Belum dimulai | - | - |
| Task 14 | Performance Optimization | ⏳ Belum dimulai | - | - |
| Task 15 | Oracle Cloud Deployment | ⏳ Belum dimulai | - | - |
| Task 16 | Social Login | ⏳ Belum dimulai | - | - |
| Task 17 | Guest Checkout | ⏳ Belum dimulai | - | - |
| Task 18 | Live Chat & Support | ⏳ Belum dimulai | - | - |
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
- Added Eloquent models for all Task 2 domain tables and blueprint relationships
- Added Spatie HasRoles and helper methods to User
- Added relationship methods, casts, and guarded mass-assignment defaults
- Validated model loading with tinker relationship checks
```

---

## 🗄️ 4. STATUS DATABASE

<!-- Codex update setelah Task 2 selesai -->

```
Migrations: Berhasil dijalankan dengan php artisan migrate:fresh --force
Seeders: Belum dijalankan
Tables:
- Task 2 domain tables: users, vendors, categories, size_guides, products, product_attributes,
  product_attribute_values, product_variants, addresses, cart_items, orders, order_items,
  dropship_orders, shipping_zones, shipping_rates, reviews, banners, coupons, wishlists,
  loyalty_points, loyalty_transactions, stock_notifications, return_requests, affiliates,
  affiliate_clicks, affiliate_commissions, affiliate_payout_methods, affiliate_payouts,
  support_tickets, ticket_replies, newsletter_subscribers, product_questions, product_answers,
  faqs
- Package/framework tables also present: password_reset_tokens, sessions, cache, jobs,
  permissions/roles pivot tables, media
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
```

---

## 🔧 8. TOOLS YANG TERSEDIA DI WINDOWS

<!-- Codex update setelah Task 1 selesai -->

```
Git:      Belum dicek
PHP:      Belum dicek
Composer: Belum dicek
Node.js:  Belum dicek
npm:      Belum dicek
MySQL:    Belum dicek
Redis:    Belum dicek
```

---

## ➡️ 9. LANGKAH BERIKUTNYA

<!-- Codex SELALU update bagian ini setelah setiap task -->

```
Task berikutnya: Task 4 — Services Layer
Branch yang akan dibuat: codex/task-4-services-layer
Instruksi lengkap: Lihat BLUEPRINT_COMPLETE.md Task 4
```

---

## 📝 10. CATATAN PENTING

<!-- Codex catat hal-hal penting yang perlu diingat -->

```
- Redis tidak tersedia di Windows → Gunakan CACHE_DRIVER=file
- Stripe Webhook HARUS exclude dari CSRF middleware
- vendor_price JANGAN PERNAH ditampilkan ke storefront
- Semua harga disimpan dalam USD di database
- Konversi mata uang hanya dilakukan di frontend/view
```

---

## 🕐 11. RIWAYAT UPDATE

<!-- Codex catat setiap kali file ini diupdate -->

| Tanggal | Update | Oleh |
|---------|--------|------|
| 2026-05-10 | Task 3 models dan relationships selesai, menunggu PR review | Codex |
| 2026-05-08 | Task 2 migrations selesai dan tervalidasi lokal | Codex |
| 2026-05-08 | Task 1 ditandai selesai setelah PR merge | Codex |
| [DATE] | File dibuat pertama kali | Codex |

---

*File ini adalah "memori permanen" project.*
*Codex WAJIB update file ini setelah setiap task selesai.*
*Owner JANGAN edit file ini secara manual.*
