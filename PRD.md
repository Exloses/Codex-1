# PRD — Product Requirements Document
# Global Multi-Vendor Dropship Platform
> Versi: 1.0
> Status: Active Development
> Repository: Exloses/Codex-1
> Stack: Laravel 11 + Vue 3 + Inertia.js + Filament v3

---

## 1. RINGKASAN PRODUK

### Nama Produk
GlobalDrop — Multi-Vendor Dropship Platform

### Tagline
*"Shop Global, Sourced Local"*

### Deskripsi Singkat
Platform e-commerce dropship global yang menghubungkan supplier lokal Indonesia dengan pembeli dari seluruh dunia. Vendor mendaftarkan produk, platform menjual ke pembeli global, dan vendor mengirim langsung ke pembeli tanpa pembeli tahu identitas vendor.

### Tujuan Utama
1. Membantu supplier Indonesia menjual produk ke pasar global
2. Memberikan pembeli global akses ke produk berkualitas dari Indonesia
3. Platform mengambil margin dari selisih harga jual dan harga vendor
4. Membangun ekosistem affiliate global untuk pertumbuhan organik

---

## 2. TARGET PASAR

### Pasar Utama
- **Pembeli:** Seluruh dunia (fokus: USA, Eropa, Australia, Asia Tenggara)
- **Vendor/Supplier:** Indonesia (lokal)
- **Affiliate:** Global (siapa saja bisa daftar)

### Ukuran Pasar
- Global e-commerce market: $6.3 Triliun (2024)
- Dropshipping market: $301 Miliar (2024)
- Indonesia export products: Fashion, Kerajinan, Elektronik, Makanan

---

## 3. USER PERSONAS

### Persona 1 — BUYER (Pembeli Global)
```
Nama:     Sarah
Lokasi:   Melbourne, Australia
Usia:     28 tahun
Pekerjaan: Marketing Manager

Kebutuhan:
- Produk unik yang tidak ada di toko lokal
- Harga kompetitif dalam AUD
- Pengiriman yang bisa dilacak
- Pembayaran aman (kartu kredit / PayPal)
- Return policy yang jelas

Pain Points:
- Susah temukan produk Indonesia berkualitas
- Tidak tahu cara beli langsung dari Indonesia
- Khawatir soal penipuan online
- Tidak bisa bayar dalam Rupiah
```

### Persona 2 — VENDOR (Supplier Indonesia)
```
Nama:     Pak Budi
Lokasi:   Bandung, Indonesia
Usia:     42 tahun
Bisnis:   Produsen tas kulit

Kebutuhan:
- Jangkauan pasar internasional
- Tidak perlu kelola website sendiri
- Terima pembayaran dalam Rupiah
- Dashboard sederhana untuk kelola produk
- Notifikasi order yang jelas

Pain Points:
- Tidak bisa bahasa Inggris dengan baik
- Tidak paham cara jualan online internasional
- Tidak punya akses payment gateway global
- Takut barang tidak dibayar
```

### Persona 3 — AFFILIATE
```
Nama:     Miguel
Lokasi:   Madrid, Spanyol
Usia:     25 tahun
Pekerjaan: Content Creator / Blogger

Kebutuhan:
- Link unik yang mudah dibagikan
- Dashboard performa yang jelas
- Komisi yang kompetitif
- Pencairan mudah via PayPal

Pain Points:
- Tidak punya produk sendiri untuk dijual
- Ingin monetize audience-nya
- Butuh platform yang bisa dipercaya
```

### Persona 4 — ADMIN (Pengelola Platform)
```
Kebutuhan:
- Monitor semua transaksi
- Approve/reject vendor
- Kelola komisi dan payout
- Laporan keuangan lengkap
- Pantau performa affiliate
```

---

## 4. FITUR LENGKAP PER ROLE

### 4.1 BUYER (Pembeli)

#### Akun & Auth
- [ ] Register dengan email + password
- [ ] Login dengan Google / Facebook
- [ ] Guest checkout (beli tanpa daftar)
- [ ] Reset password via email
- [ ] Profil: nama, foto, preferensi bahasa & mata uang

#### Belanja
- [ ] Browse produk dengan filter & search
- [ ] Lihat detail produk + gambar zoom
- [ ] Pilih variant (warna, ukuran, material)
- [ ] Lihat size guide per kategori
- [ ] Tambah ke keranjang
- [ ] Simpan ke wishlist
- [ ] Bandingkan produk
- [ ] Lihat produk yang baru dilihat
- [ ] Product Q&A (tanya sebelum beli)

#### Checkout & Pembayaran
- [ ] Multi-step checkout
- [ ] Input/pilih alamat pengiriman global
- [ ] Pilih opsi pengiriman (Standard/Express)
- [ ] Estimasi ongkir real-time
- [ ] Apply kode coupon
- [ ] Redeem loyalty points
- [ ] Bayar dengan Stripe (kartu kredit)
- [ ] Bayar dengan PayPal
- [ ] Harga tampil dalam mata uang lokal

#### Pasca Pembelian
- [ ] Email konfirmasi pesanan
- [ ] Tracking pesanan real-time
- [ ] Download invoice PDF
- [ ] Tulis ulasan produk (dapat loyalty points)
- [ ] Ajukan return/refund
- [ ] Buka support ticket

#### Engagement
- [ ] Loyalty points (10 poin per $1)
- [ ] Notifikasi in-app
- [ ] Stock notification ("Notify Me")
- [ ] Price drop alert
- [ ] Newsletter subscription
- [ ] Affiliate program (daftar gratis)

---

### 4.2 VENDOR (Supplier Indonesia)

#### Registrasi & Onboarding
- [ ] Daftar sebagai vendor (apply dari akun buyer)
- [ ] Isi profil toko (nama, deskripsi, kota)
- [ ] Upload logo dan banner toko
- [ ] Input info bank untuk pencairan
- [ ] Tunggu approval dari admin

#### Kelola Produk
- [ ] Tambah produk (nama EN + ID, deskripsi, foto)
- [ ] Upload multiple foto (max 5, via Cloudinary)
- [ ] Set harga vendor (dalam IDR)
- [ ] Harga jual USD diset oleh admin
- [ ] Tambah variant produk (warna, ukuran, dll)
- [ ] Upload size guide
- [ ] Kelola stok
- [ ] Aktif/nonaktifkan produk

#### Kelola Pesanan (Dropship Orders)
- [ ] Terima notifikasi order baru
- [ ] Konfirmasi bisa memproses order
- [ ] Update status: Processing → Shipped
- [ ] Input nomor resi pengiriman + kurir
- [ ] Lihat alamat pengiriman pembeli global
- [ ] **PENTING: Tidak tahu harga jual platform**

#### Keuangan
- [ ] Lihat saldo IDR tersedia
- [ ] Riwayat pendapatan per order
- [ ] Request pencairan ke rekening bank
- [ ] Status pencairan (pending/paid)

#### Dashboard
- [ ] Statistik: produk aktif, order baru, pendapatan
- [ ] Grafik penjualan 30 hari
- [ ] 5 order terbaru yang perlu diproses

---

### 4.3 AFFILIATE

#### Program
- [ ] Daftar gratis (siapa saja bisa)
- [ ] Dapat kode unik + link referral
- [ ] Buat link untuk produk spesifik
- [ ] Share ke media sosial

#### Komisi & Tier
- [ ] Starter: 5% (default)
- [ ] Silver: 7% (total earned > $500)
- [ ] Gold: 10% (total earned > $2.000)
- [ ] Platinum: 15% (total earned > $10.000)
- [ ] Upgrade tier otomatis
- [ ] Komisi hold 7 hari (anti-fraud)

#### Dashboard
- [ ] Total klik, penjualan, komisi
- [ ] Grafik performa 30 hari
- [ ] Riwayat komisi per transaksi
- [ ] Status: Pending → Available → Paid

#### Pencairan
- [ ] Minimum withdraw: $20
- [ ] Metode: PayPal, Wise, Bank Transfer
- [ ] Admin fee: 2% (min $1)
- [ ] Approval dari admin

---

### 4.4 ADMIN

#### Dashboard
- [ ] Revenue hari ini / bulan ini (USD)
- [ ] Total order, vendor aktif, affiliate aktif
- [ ] Pending approvals (vendor, payout)
- [ ] Open support tickets
- [ ] Grafik revenue + orders

#### Manajemen
- [ ] CRUD User (aktif/nonaktif)
- [ ] Approve/reject vendor + set komisi
- [ ] CRUD Produk + set harga jual USD
- [ ] Monitor semua pesanan
- [ ] Monitor dropship orders
- [ ] Approve return requests + proses refund

#### Affiliate
- [ ] List semua affiliate + stats
- [ ] Set komisi custom per affiliate
- [ ] Approve/reject payout requests
- [ ] Mark payout as paid + input transaction ref

#### Marketing
- [ ] CRUD Banner homepage
- [ ] CRUD Coupon/voucher
- [ ] CRUD FAQ
- [ ] Kelola newsletter subscribers
- [ ] Kirim broadcast email

#### Keuangan
- [ ] Platform revenue (margin)
- [ ] Vendor payables
- [ ] Affiliate payouts
- [ ] Export laporan ke Excel

#### Pengaturan
- [ ] Shipping zones + rates
- [ ] Komisi tier affiliate global
- [ ] Minimum withdraw
- [ ] Hold period komisi
- [ ] Platform info (nama, logo, dll)

---

## 5. ALUR BISNIS DROPSHIP

```
ALUR PEMBELIAN:

1. Buyer temukan produk di Storefront
           ↓
2. Buyer pilih variant, tambah ke cart
           ↓
3. Buyer checkout → input alamat global
           ↓
4. Sistem hitung ongkir (EasyPost/Zone)
           ↓
5. Buyer bayar (Stripe/PayPal dalam mata uang lokal)
           ↓
6. Sistem otomatis:
   - Buat Dropship Order ke Vendor
   - Hitung komisi Affiliate (jika ada)
   - Tambah Loyalty Points ke Buyer
   - Kurangi stok produk
   - Kirim email konfirmasi ke Buyer
   - Kirim notif ke Vendor
           ↓
7. Vendor konfirmasi & kemas produk
           ↓
8. Vendor input resi pengiriman
           ↓
9. Sistem kirim email tracking ke Buyer
           ↓
10. Buyer terima produk
           ↓
11. Admin bayar Vendor (dalam IDR)
           ↓
12. Platform ambil margin (selisih harga)
```

---

## 6. MODEL MONETISASI

### Sumber Pendapatan Platform:

| Sumber | Cara | Estimasi |
|--------|------|----------|
| **Margin Produk** | Harga Jual USD - Harga Vendor IDR | 20-50% per produk |
| **Admin Fee Affiliate** | 2% dari setiap payout | Per withdrawal |
| **Featured Product** | Vendor bayar untuk produk tampil di atas | (future) |
| **Premium Vendor** | Fitur extra untuk vendor berbayar | (future) |

### Contoh Perhitungan Margin:
```
Harga Vendor:  Rp 150.000 (≈ $9.50 USD)
Harga Jual:    $25.00 USD
Ongkir:        $8.00 USD (ditanggung buyer)
───────────────────────────────
Platform dapat: $25.00 - $9.50 = $15.50 USD
Margin:         62% per transaksi
```

---

## 7. KEBUTUHAN NON-FUNGSIONAL

### Performa
- Halaman load < 3 detik
- API response < 500ms
- Target: 1.000 - 10.000 pengunjung/hari
- Cache dengan Redis untuk data statis

### Keamanan
- HTTPS wajib di production
- 9 lapisan keamanan (rate limit, helmet, CSRF, dll)
- Data kartu kredit tidak disimpan (Stripe handle)
- Vendor price tidak pernah terekspos ke buyer
- JWT untuk autentikasi API

### Ketersediaan
- Target uptime: 99.9%
- Server: Oracle Cloud (Linux)
- Queue worker: Supervisor (auto-restart)
- Backup database: harian

### Skalabilitas
- Redis untuk caching & queue
- Cloudinary untuk media (CDN global)
- MySQL dengan indexing optimal
- PM2 cluster mode untuk production

---

## 8. INTEGRASI PIHAK KETIGA

| Layanan | Fungsi | Tier |
|---------|--------|------|
| **Stripe** | Pembayaran kartu kredit global | Wajib |
| **PayPal** | Pembayaran alternatif | Wajib |
| **EasyPost** | Shipping rates + label internasional | Wajib |
| **Cloudinary** | Upload & CDN gambar | Wajib |
| **Resend** | Email transaksional | Wajib |
| **Open Exchange Rates** | Konversi mata uang | Wajib |
| **Tawk.to** | Live chat gratis | Wajib |
| **Google OAuth** | Social login | Penting |
| **Facebook OAuth** | Social login | Penting |
| **Wise** | Affiliate payout internasional | Penting |

---

## 9. KRITERIA SUKSES (KPI)

### 3 Bulan Pertama (Launch)
```
□ 10+ vendor aktif
□ 100+ produk terdaftar
□ 50+ transaksi pertama
□ 20+ affiliate aktif
□ Rating toko > 4.0/5.0
```

### 6 Bulan
```
□ 50+ vendor aktif
□ 500+ produk
□ $10.000+ total GMV (Gross Merchandise Value)
□ 100+ affiliate aktif
□ Return rate < 5%
```

### 12 Bulan
```
□ 200+ vendor aktif
□ 2.000+ produk
□ $100.000+ total GMV
□ 500+ affiliate aktif
□ Ekspansi ke 3+ kategori utama
```

---

## 10. ROADMAP DEVELOPMENT

### Phase 1 — Foundation (Task 1-15) ✅ 73%
```
✅ Task 1  — Laravel Installation
✅ Task 2  — Database Migrations
✅ Task 3  — Models & Relationships
✅ Task 4  — Services Layer
✅ Task 5  — Background Jobs
✅ Task 6  — Schedulers
✅ Task 7  — Routes
✅ Task 8  — Controllers
✅ Task 9  — Filament Admin Panel
✅ Task 10 — Vue Frontend
🔄 Task 11 — Security Middleware
⏳ Task 12 — Email Notifications
⏳ Task 13 — Database Seeders
⏳ Task 14 — Performance
⏳ Task 15 — Deploy Oracle Cloud
```

### Phase 2 — Customer Experience (Task 16-30) ⏳
```
⏳ Task 16 — Social Login
⏳ Task 17 — Guest Checkout
⏳ Task 18 — Live Chat & Support
⏳ Task 19 — Wishlist
⏳ Task 20 — Product Variants
⏳ Task 21 — Order Tracking
⏳ Task 22 — Return & Refund
⏳ Task 23 — Loyalty Points
⏳ Task 24 — Notification Center
⏳ Task 25 — Newsletter
⏳ Task 26 — Stock & Price Alerts
⏳ Task 27 — Product Q&A
⏳ Task 28 — PDF Invoice
⏳ Task 29 — PWA
⏳ Task 30 — FAQ & Help Center
```

### Phase 3 — Growth (Future)
```
⏳ Mobile App (React Native)
⏳ Advanced Analytics Dashboard
⏳ AI Product Recommendation
⏳ Multi-language AI Translation
⏳ Vendor Premium Features
⏳ B2B Wholesale Module
```

---

## 11. RISIKO & MITIGASI

| Risiko | Kemungkinan | Dampak | Mitigasi |
|--------|-------------|--------|----------|
| Vendor tidak kirim tepat waktu | Tinggi | Tinggi | Rating sistem + suspend vendor |
| Produk tidak sesuai deskripsi | Sedang | Tinggi | Return policy + buyer protection |
| Fraud pembayaran | Rendah | Tinggi | Stripe fraud detection |
| Vendor curang (self-referral affiliate) | Sedang | Sedang | Sistem deteksi otomatis |
| Server down | Rendah | Tinggi | Oracle Cloud + monitoring |
| Kurs mata uang fluktuasi | Tinggi | Sedang | Update kurs setiap 1 jam |

---

*PRD — GlobalDrop Multi-Vendor Dropship Platform*
*Dibuat: Mei 2026 | Versi: 1.0*
*Repository: Exloses/Codex-1*
