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
| Task 1 | Laravel Installation | ⏳ Belum dimulai | - | - |
| Task 2 | Database Migrations | ⏳ Belum dimulai | - | - |
| Task 3 | Models & Relationships | ⏳ Belum dimulai | - | - |
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
Belum ada file yang dibuat.
```

---

## 🗄️ 4. STATUS DATABASE

<!-- Codex update setelah Task 2 selesai -->

```
Migrations: Belum dijalankan
Seeders: Belum dijalankan
Tables: -
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
Task berikutnya: Task 1 — Laravel Installation
Branch yang akan dibuat: codex/task-1-laravel-installation
Instruksi lengkap: Lihat BLUEPRINT_COMPLETE.md Task 1
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
| [DATE] | File dibuat pertama kali | Codex |

---

*File ini adalah "memori permanen" project.*
*Codex WAJIB update file ini setelah setiap task selesai.*
*Owner JANGAN edit file ini secara manual.*
