# AGENTS.md — Codex Agent Rules & Memory
> ⚠️ Codex membaca file ini OTOMATIS setiap sesi.
> Ini adalah aturan permanen yang HARUS diikuti di semua task.

---

## 🎯 IDENTITAS PROYEK

- **Nama:** Global Multi-Vendor Dropship Platform
- **Repository:** Exloses/Codex-1
- **Blueprint:** Baca `BLUEPRINT_COMPLETE.md` untuk detail lengkap
- **Environment:** Windows localhost (bukan production)
- **Stack:** Laravel 11 + Vue 3 + Inertia.js + Tailwind CSS + Filament v3

---

## 🔒 ATURAN KEAMANAN (TIDAK BOLEH DILANGGAR)

```
❌ JANGAN PERNAH commit file .env (hanya .env.example)
❌ JANGAN PERNAH commit API key, token, password, atau secret asli
❌ JANGAN PERNAH commit APP_KEY Laravel yang asli
❌ JANGAN PERNAH push ke branch main/master secara langsung
❌ JANGAN PERNAH merge PR sendiri — biarkan owner yang merge
❌ JANGAN PERNAH hapus file penting tanpa konfirmasi
❌ JANGAN PERNAH deploy ke production atau konfigurasi Oracle Cloud
❌ JANGAN PERNAH gunakan API key Stripe/PayPal/dll yang asli
```

### Nilai placeholder yang AMAN digunakan di .env.example:
```env
APP_KEY=base64:PLACEHOLDER_GENERATE_WITH_PHP_ARTISAN_KEY_GENERATE
DB_PASSWORD=YOUR_DB_PASSWORD_HERE
STRIPE_KEY=pk_test_YOUR_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_YOUR_STRIPE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET
PAYPAL_CLIENT_ID=YOUR_PAYPAL_CLIENT_ID_HERE
PAYPAL_CLIENT_SECRET=YOUR_PAYPAL_CLIENT_SECRET_HERE
EASYPOST_API_KEY=EZTKXXXXXXXXXXXXXXXXXXXXXXXX
OPEN_EXCHANGE_APP_ID=YOUR_OPEN_EXCHANGE_APP_ID
CLOUDINARY_URL=cloudinary://YOUR_API_KEY:YOUR_API_SECRET@YOUR_CLOUD_NAME
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
FACEBOOK_CLIENT_ID=YOUR_FACEBOOK_APP_ID
FACEBOOK_CLIENT_SECRET=YOUR_FACEBOOK_APP_SECRET
RESEND_API_KEY=re_YOUR_RESEND_API_KEY
VITE_TAWK_PROPERTY_ID=YOUR_TAWK_PROPERTY_ID
VITE_TAWK_WIDGET_ID=YOUR_TAWK_WIDGET_ID
```

---

## 🌿 GIT WORKFLOW (WAJIB DIIKUTI SETIAP TASK)

```
Setiap task → 1 branch baru → 1 Pull Request
JANGAN merge sendiri
```

### Nama Branch per Task:
| Task | Branch Name |
|------|-------------|
| Task 1 | `codex/task-1-laravel-installation` |
| Task 2 | `codex/task-2-database-migrations` |
| Task 3 | `codex/task-3-models-relationships` |
| Task 4 | `codex/task-4-services-layer` |
| Task 5 | `codex/task-5-background-jobs` |
| Task 6 | `codex/task-6-schedulers-commands` |
| Task 7 | `codex/task-7-routes` |
| Task 8 | `codex/task-8-controllers` |
| Task 9 | `codex/task-9-filament-admin` |
| Task 10 | `codex/task-10-vue-frontend` |
| Task 11 | `codex/task-11-security-middleware` |
| Task 12 | `codex/task-12-email-notifications` |
| Task 13 | `codex/task-13-database-seeders` |
| Task 14 | `codex/task-14-performance` |
| Task 15 | `codex/task-15-deploy-oracle` |
| Task 16 | `codex/task-16-social-login` |
| Task 17 | `codex/task-17-guest-checkout` |
| Task 18 | `codex/task-18-livechat-support` |
| Task 19 | `codex/task-19-wishlist` |
| Task 20 | `codex/task-20-product-variants` |
| Task 21 | `codex/task-21-order-tracking` |
| Task 22 | `codex/task-22-return-refund` |
| Task 23 | `codex/task-23-loyalty-points` |
| Task 24 | `codex/task-24-notifications` |
| Task 25 | `codex/task-25-newsletter` |
| Task 26 | `codex/task-26-stock-alerts` |
| Task 27 | `codex/task-27-product-qa` |
| Task 28 | `codex/task-28-pdf-invoice` |
| Task 29 | `codex/task-29-pwa` |
| Task 30 | `codex/task-30-faq-helpcenter` |

### Langkah Git setiap task:
```bash
# 1. Pastikan di branch main dan up to date
git checkout main
git pull origin main

# 2. Buat branch baru
git checkout -b codex/task-X-nama-task

# 3. Kerjakan task...

# 4. Setelah selesai
git add .
git commit -m "feat: Task X - [deskripsi singkat]"
git push origin codex/task-X-nama-task

# 5. Buat PR di GitHub
# Title: "Task X: [Nama Task]"
# JANGAN merge sendiri
```

---

## 💻 ENVIRONMENT WINDOWS

### Path yang umum di Windows:
```
PHP:      C:\laragon\bin\php\php8.3\php.exe
          C:\xampp\php\php.exe
          C:\Users\[nama]\AppData\Roaming\Herd\bin\php.exe
MySQL:    C:\laragon\bin\mysql\mysql-8.0\bin\mysql.exe
          C:\xampp\mysql\bin\mysql.exe
Redis:    C:\Program Files\Redis\redis-server.exe
Composer: C:\ProgramData\ComposerSetup\bin\composer.bat
Node:     C:\Program Files\nodejs\node.exe
npm:      C:\Program Files\nodejs\npm.cmd
```

### Jika Redis tidak tersedia di Windows:
```php
// Di .env sementara (JANGAN commit .env)
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```
Redis tidak boleh memblokir task lain. Lanjutkan dengan file driver.

### Jika port 8000 sudah dipakai:
```bash
php artisan serve --port=8080
```

### Line endings Windows:
```bash
# Set Git untuk handle line endings
git config core.autocrlf true
```

---

## 📋 ATURAN PENGERJAAN TASK

### Sebelum mulai setiap task:
1. Baca instruksi task di `BLUEPRINT_COMPLETE.md`
2. Buat branch baru sesuai tabel di atas
3. Cek apakah ada file dari task sebelumnya yang perlu dibaca

### Saat mengerjakan:
1. Buat file LENGKAP — jangan setengah jadi
2. Tambahkan komentar pada kode yang kompleks
3. Jika ada error, perbaiki dulu sebelum lanjut
4. Jangan skip bagian dari task

### Setelah selesai:
1. Jalankan validation commands
2. Pastikan tidak ada file .env atau secret yang ter-commit
3. Push dan buat PR
4. Laporan lengkap (lihat format di bawah)

---

## 📊 FORMAT LAPORAN SETELAH SETIAP TASK

Setelah task selesai, berikan laporan dengan format ini:

```
## ✅ Task [X] — [Nama Task] SELESAI

### Tools/Dependencies
- ✅ [tool] — [versi]
- ❌ [tool] — TIDAK DITEMUKAN (langkah manual: ...)

### File yang Dibuat
- path/ke/file1.php
- path/ke/file2.vue
- ...

### File yang Dimodifikasi
- path/ke/file3.php (tambahan: ...)

### Commands yang Dijalankan
- composer install ✅
- npm install ✅
- php artisan migrate ✅ / ❌ [error message]

### Hasil Validasi
- php artisan about: ✅ / ❌
- php artisan route:list: ✅ / ❌
- npm run build: ✅ / ❌

### Pull Request
- Branch: codex/task-X-nama-task
- PR URL: https://github.com/Exloses/Codex-1/pull/[nomor]

### Langkah Manual untuk Owner
Jalankan perintah ini di Windows Anda:
1. git pull origin main (setelah merge PR)
2. php artisan migrate
3. php artisan serve
4. Buka http://localhost:8000

### Masalah yang Ditemukan
- [Jika ada masalah, jelaskan di sini]

### Siap untuk Task Berikutnya?
- ✅ Ya, Task [X+1] bisa dimulai
- ❌ Tidak, masalah ini harus diselesaikan dulu: [...]
```

---

## 🚨 JIKA CODEX STUCK / TIDAK BISA LANJUT

Jika menghadapi situasi berikut, **BERHENTI dan lapor ke owner:**

1. Diperlukan UAC/Administrator permission untuk install software
2. Port yang dibutuhkan sudah dipakai dan tidak bisa diganti
3. Konflik dependency yang tidak bisa diselesaikan otomatis
4. Database tidak bisa diakses sama sekali
5. Error yang tidak bisa diselesaikan setelah 3 percobaan

### Format laporan jika stuck:
```
## ⛔ Task [X] — BLOCKED

### Masalah:
[Deskripsi masalah]

### Sudah Dicoba:
1. [cara 1]
2. [cara 2]
3. [cara 3]

### Yang Dibutuhkan dari Owner:
[Instruksi spesifik yang perlu dilakukan owner]

### Sementara Ini:
[Apa yang sudah berhasil dikerjakan sebelum blocked]
```

---

## 🔄 URUTAN TASK (REFERENSI CEPAT)

```
BAGIAN 1 — FOUNDATION
Task 1  → Instalasi Laravel + semua package
Task 2  → 34 Database migrations
Task 3  → Models & Relationships
Task 4  → Services Layer (Stripe, PayPal, EasyPost, dll)
Task 5  → Background Jobs (Queue)
Task 6  → Artisan Commands & Scheduler
Task 7  → Routes lengkap
Task 8  → Semua Controllers
Task 9  → Filament Admin Panel
Task 10 → Vue Frontend (Pages & Components)
Task 11 → Security Middleware
Task 12 → Email Notifications
Task 13 → Database Seeders
Task 14 → Performance Optimization
Task 15 → Deploy Oracle Cloud

BAGIAN 2 — CUSTOMER EXPERIENCE
Task 16 → Social Login (Google + Facebook)
Task 17 → Guest Checkout
Task 18 → Live Chat (Tawk.to) + Support Ticket
Task 19 → Wishlist
Task 20 → Product Variants (Warna, Ukuran, dll)
Task 21 → Order Tracking Real-time
Task 22 → Return & Refund System
Task 23 → Loyalty Points
Task 24 → Notification Center
Task 25 → Newsletter
Task 26 → Stock Notification + Price Alert
Task 27 → Product Q&A
Task 28 → PDF Invoice Download
Task 29 → PWA (Progressive Web App)
Task 30 → FAQ & Help Center
```

---

*AGENTS.md — Dibuat untuk Codex Agent*
*Repository: Exloses/Codex-1*
*Versi: 1.0*
