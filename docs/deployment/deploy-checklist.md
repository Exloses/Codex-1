# Oracle Cloud Manual Deployment Checklist

Status: manual checklist untuk nanti. Jangan dieksekusi otomatis sekarang. Deployment aktual ditunda sampai Oracle Cloud account/server tersedia.

## Pre-Deploy

- [ ] PR deployment yang akan dirilis sudah merged oleh owner.
- [ ] Commit/tag release sudah ditentukan.
- [ ] Domain production sudah dipilih, misalnya `yourdomain.com`.
- [ ] Owner sudah menyiapkan credential production di password manager, bukan di repository.
- [ ] `.env.production.example` sudah direview dan tidak berisi secret asli.
- [ ] Maintenance window atau launch window sudah disetujui.

## Server Provisioning

- [ ] Oracle Cloud account aktif.
- [ ] VM Ubuntu LTS dibuat.
- [ ] SSH key owner terpasang.
- [ ] Public IP dan DNS tersedia.
- [ ] Security list/network security group membuka port 22, 80, dan 443 sesuai kebutuhan.
- [ ] Firewall OS dikonfigurasi setelah akses SSH diverifikasi.

## System Packages

- [ ] `apt update` dan `apt upgrade` selesai.
- [ ] Nginx terinstall.
- [ ] Git, curl, unzip, dan Supervisor terinstall.
- [ ] Composer terinstall dari sumber resmi.
- [ ] Node.js LTS dan npm terinstall.

## PHP Extensions

- [ ] PHP 8.3 CLI terinstall.
- [ ] PHP 8.3 FPM terinstall dan running.
- [ ] `php8.3-mysql` terinstall.
- [ ] `php8.3-redis` terinstall.
- [ ] `php8.3-curl` terinstall.
- [ ] `php8.3-mbstring` terinstall.
- [ ] `php8.3-xml` terinstall.
- [ ] `php8.3-zip` terinstall.
- [ ] `php8.3-gd` terinstall.
- [ ] `php8.3-intl` terinstall.
- [ ] `php8.3-bcmath` terinstall.

## MySQL / Redis

- [ ] MySQL service running.
- [ ] Database production dibuat.
- [ ] User database production dibuat dengan password kuat.
- [ ] Redis service running.
- [ ] Redis hanya dapat diakses lokal atau jaringan private yang diizinkan.
- [ ] Backup database manual berhasil diuji minimal sekali.

## App Setup

- [ ] Direktori `/var/www/dropship-platform` dibuat.
- [ ] Repository diclone ke `/var/www/dropship-platform`.
- [ ] Release commit/tag yang benar sudah checkout.
- [ ] Ownership direktori diset ke user web yang tepat.
- [ ] `composer install --no-dev --optimize-autoloader` selesai.

## Environment Setup

- [ ] `.env.production.example` dicopy ke `.env` di server.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL=https://yourdomain.com`.
- [ ] Database, Redis, mail, payment, shipping, currency, Cloudinary, OAuth, dan Tawk placeholders diganti di server.
- [ ] `php artisan key:generate --force` dijalankan di server.
- [ ] Tidak ada credential production masuk ke Git.

## Build Frontend

- [ ] `npm ci` selesai.
- [ ] `npm run build` selesai.
- [ ] Direktori `public/build` tersedia.

## Migrate Database

- [ ] `php artisan migrate --force` selesai.
- [ ] Seeder production hanya dijalankan jika owner menyetujui.
- [ ] `php artisan storage:link` selesai.
- [ ] Permission `storage` dan `bootstrap/cache` benar.

## Queue Worker

- [ ] `docs/deployment/supervisor-laravel-worker.conf` disalin ke `/etc/supervisor/conf.d/laravel-worker.conf`.
- [ ] `supervisorctl reread` selesai.
- [ ] `supervisorctl update` selesai.
- [ ] `supervisorctl status laravel-worker:*` menunjukkan worker running.
- [ ] Log worker masuk ke `storage/logs/worker.log`.

## Scheduler

- [ ] Cron user `www-data` berisi entry:

  ```cron
  * * * * * cd /var/www/dropship-platform && php artisan schedule:run >> /dev/null 2>&1
  ```

- [ ] Scheduler berjalan tanpa error setelah minimal satu menit.

## Nginx

- [ ] `docs/deployment/nginx-dropship-platform.conf` disalin ke sites-available.
- [ ] Symlink ke sites-enabled dibuat.
- [ ] `server_name` diganti dari placeholder ke domain production.
- [ ] `nginx -t` sukses.
- [ ] Nginx reload sukses.
- [ ] File tersembunyi seperti `.env` tidak dapat diakses via HTTP.

## SSL

- [ ] DNS mengarah ke public IP server.
- [ ] Certbot terinstall.
- [ ] Certificate untuk `yourdomain.com` dan `www.yourdomain.com` diterbitkan.
- [ ] HTTP redirect ke HTTPS aktif.
- [ ] `certbot renew --dry-run` sukses.

## Smoke Test

- [ ] Homepage HTTPS render tanpa error.
- [ ] Product listing render tanpa error.
- [ ] Product detail render dan tidak mengekspos `vendor_price`.
- [ ] Login/register render.
- [ ] Admin panel membutuhkan login.
- [ ] Queue worker running.
- [ ] Scheduler running.
- [ ] Mail test berhasil.
- [ ] Payment provider webhook belum diaktifkan sebelum HTTPS dan credential production valid.
- [ ] `storage/logs/laravel.log` tidak berisi error kritis baru.

## Rollback

- [ ] Commit/tag terakhir yang stabil dicatat.
- [ ] Backup database terakhir tersedia.
- [ ] Cara checkout release sebelumnya sudah diketahui.
- [ ] Cara refresh Composer, asset build, cache Laravel, Supervisor, dan Nginx sudah siap.
- [ ] Jika migration destructive pernah dijalankan, rollback database harus diputuskan oleh owner sebelum eksekusi.
