# Oracle Cloud Deployment Preparation

Status: deployment aktual ditunda karena Oracle Cloud account/server belum tersedia. Dokumen ini hanya panduan persiapan manual untuk nanti. Jangan menjalankan command ini ke server production dari Windows localhost saat Task 15.

## Tujuan Deployment

Menyiapkan GlobalDropship, aplikasi Laravel 11 + Vue 3 + Inertia.js + Filament v3, agar dapat dideploy ke VM Ubuntu di Oracle Cloud setelah akun dan server tersedia. Target production menggunakan Nginx, PHP-FPM 8.3, MySQL, Redis, Supervisor, SSL/HTTPS, dan build asset Vite.

## Prasyarat Oracle Cloud

- Oracle Cloud account aktif dan sudah lolos verifikasi administrasi.
- Compartment, VCN, subnet, internet gateway, route table, dan security list/network security group sudah disiapkan.
- Public IPv4 atau domain yang mengarah ke public IP VM.
- Akses SSH key milik owner, bukan credential yang disimpan di repository.
- Backup storage atau Object Storage bucket untuk backup database/media jika nanti dipakai.

## Prasyarat VM Ubuntu

- Ubuntu LTS yang masih didukung, direkomendasikan Ubuntu 24.04 LTS atau 22.04 LTS.
- User non-root dengan akses `sudo`.
- Port inbound minimal:
  - `22/tcp` untuk SSH, dibatasi ke IP owner jika memungkinkan.
  - `80/tcp` untuk HTTP challenge/redirect.
  - `443/tcp` untuk HTTPS.
- Firewall OS seperti UFW aktif setelah rule diverifikasi.
- Timezone server, NTP, dan update package berjalan normal.

## Spesifikasi Minimal Server

- 2 vCPU.
- 2 GB RAM minimum, 4 GB RAM direkomendasikan untuk build asset dan queue worker.
- 50 GB block volume.
- Swap 2 GB jika RAM kecil.
- MySQL dan Redis dapat satu VM untuk fase awal; pisahkan layanan saat traffic meningkat.

## Security Checklist

- Jangan commit `.env`, SSH key, token, password, atau secret asli.
- Gunakan `.env.production.example` sebagai template, lalu isi `.env` langsung di server oleh owner.
- Set `APP_ENV=production` dan `APP_DEBUG=false`.
- Generate `APP_KEY` di server dengan `php artisan key:generate`.
- Batasi SSH dengan key-based auth dan disable password login jika siap.
- Aktifkan firewall dan expose hanya port yang diperlukan.
- Gunakan HTTPS valid sebelum menerima traffic pembeli.
- Pastikan Nginx menolak akses file tersembunyi seperti `.env`.
- Jalankan queue worker dengan user `www-data`, bukan root.
- Rotasi credential payment, OAuth, mail, Cloudinary, dan API pihak ketiga sesuai prosedur owner.

## Required Software

- Nginx.
- MySQL Server.
- Redis Server.
- PHP 8.3 + extensions Laravel:
  - `php8.3-fpm`
  - `php8.3-cli`
  - `php8.3-mysql`
  - `php8.3-redis`
  - `php8.3-curl`
  - `php8.3-mbstring`
  - `php8.3-xml`
  - `php8.3-zip`
  - `php8.3-gd`
  - `php8.3-intl`
  - `php8.3-bcmath`
  - `php8.3-tokenizer`
  - `php8.3-fileinfo`
- Composer.
- Node.js LTS and npm.
- Supervisor.
- Git.
- Certbot for SSL/HTTPS.

Example package preparation for later manual server work:

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y nginx mysql-server redis-server supervisor git unzip curl
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-redis php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath
```

Install Composer and Node.js LTS using official instructions at deployment time. Verify versions before continuing:

```bash
php -v
composer --version
node --version
npm --version
nginx -v
redis-server --version
mysql --version
supervisord --version
```

## Struktur Direktori Rekomendasi

```text
/var/www/dropship-platform
├── app
├── bootstrap
├── config
├── database
├── public
├── resources
├── routes
├── storage
├── vendor
└── .env
```

Recommended ownership:

```bash
sudo mkdir -p /var/www/dropship-platform
sudo chown -R www-data:www-data /var/www/dropship-platform
```

## Clone Repository

Run later on the prepared server:

```bash
sudo -u www-data git clone https://github.com/Exloses/Codex-1.git /var/www/dropship-platform
cd /var/www/dropship-platform
git checkout main
```

If deployment should use a tagged release or approved commit, checkout that tag/commit instead of raw `main`.

## Install Composer Dependencies

```bash
cd /var/www/dropship-platform
sudo -u www-data composer install --no-dev --optimize-autoloader
```

## Install NPM Dependencies and Build Assets

```bash
cd /var/www/dropship-platform
sudo -u www-data npm ci
sudo -u www-data npm run build
```

Use `npm install` only if `package-lock.json` is intentionally regenerated.

## Setup `.env` Production

Create the production file manually on the server:

```bash
cd /var/www/dropship-platform
sudo -u www-data cp .env.production.example .env
sudo -u www-data nano .env
```

Use placeholders from `.env.production.example`, then replace them on the server with real values owned by the project owner. Do not paste real credential values into Git history, PR comments, chat, screenshots, or issue text.

Minimum production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
DB_CONNECTION=mysql
```

## Generate APP_KEY

Generate only on the server after `.env` exists:

```bash
cd /var/www/dropship-platform
sudo -u www-data php artisan key:generate --force
```

Never copy a local development `APP_KEY` to production.

## Database Migration and Seed

Create the MySQL database and user manually first. Example placeholders:

```sql
CREATE DATABASE dropship_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dropship_user'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON dropship_platform.* TO 'dropship_user'@'localhost';
FLUSH PRIVILEGES;
```

Then run migrations after `.env` is correct:

```bash
cd /var/www/dropship-platform
sudo -u www-data php artisan migrate --force
```

Seed only when the owner confirms production seed data is intended:

```bash
sudo -u www-data php artisan db:seed --force
```

## Storage Link

```bash
cd /var/www/dropship-platform
sudo -u www-data php artisan storage:link
```

## Permissions

Laravel needs write access to `storage` and `bootstrap/cache`:

```bash
cd /var/www/dropship-platform
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

## Queue Worker Setup

Copy the sample Supervisor config:

```bash
sudo cp docs/deployment/supervisor-laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status laravel-worker:*
```

Sample file: `docs/deployment/supervisor-laravel-worker.conf`.

## Scheduler Setup

Add this cron entry for the `www-data` user:

```cron
* * * * * cd /var/www/dropship-platform && php artisan schedule:run >> /dev/null 2>&1
```

One way to edit it later:

```bash
sudo crontab -u www-data -e
```

Do not configure a production scheduler until the actual server is ready.

## Nginx Setup

Use the sample Nginx config:

```bash
sudo cp docs/deployment/nginx-dropship-platform.conf /etc/nginx/sites-available/dropship-platform
sudo ln -s /etc/nginx/sites-available/dropship-platform /etc/nginx/sites-enabled/dropship-platform
sudo nginx -t
sudo systemctl reload nginx
```

Sample file: `docs/deployment/nginx-dropship-platform.conf`.

## SSL / HTTPS

After DNS points to the server and Nginx responds on port 80, issue HTTPS certificates with Certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
sudo certbot renew --dry-run
```

HTTPS must be active before production traffic or real payment callbacks are enabled.

## Laravel Optimization

Run after `.env`, dependencies, migrations, and build are correct:

```bash
cd /var/www/dropship-platform
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

If configuration changes, refresh caches:

```bash
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

## Backup Strategy

- MySQL:
  - Schedule daily `mysqldump`.
  - Encrypt backups before moving them off-server.
  - Keep at least 7 daily and 4 weekly restore points.
- Application files:
  - Repository code is recoverable from GitHub.
  - Back up `.env`, uploaded local files under `storage/app/public` if Cloudinary is not the only media source, and Supervisor/Nginx configs.
- Redis:
  - Queue jobs should be transient; session/cache loss should not corrupt orders.
  - If Redis persistence is enabled, include Redis data in server backup policy.
- Restore drills:
  - Test restore to a non-production VM before relying on backups.

## Rollback Strategy

Preferred release process for later:

1. Record the current deployed commit:

   ```bash
   git rev-parse --short HEAD
   ```

2. Put the app in maintenance mode during risky changes:

   ```bash
   sudo -u www-data php artisan down --render="errors::503"
   ```

3. Pull or checkout the approved release commit.
4. Run dependency install, asset build, migrations, and cache refresh.
5. If rollback is needed:

   ```bash
   git checkout PREVIOUS_GOOD_COMMIT
   sudo -u www-data composer install --no-dev --optimize-autoloader
   sudo -u www-data npm ci
   sudo -u www-data npm run build
   sudo -u www-data php artisan migrate:rollback --force
   sudo -u www-data php artisan optimize:clear
   sudo -u www-data php artisan config:cache
   sudo supervisorctl restart laravel-worker:*
   sudo systemctl reload nginx
   ```

6. Bring the app back:

   ```bash
   sudo -u www-data php artisan up
   ```

Database rollback can lose data if migrations are destructive. Prefer forward fixes for schema changes after production launch.

## Post-Deployment Smoke Test Checklist

- Homepage loads over HTTPS.
- `/products` loads and product cards render.
- Product detail page loads and does not expose `vendor_price`.
- Register/login pages load.
- Admin panel `/admin` loads and requires authentication.
- Vendor dashboard requires authentication.
- Cart and checkout routes behave as expected.
- `php artisan queue:failed` returns no unexpected failures.
- Supervisor shows queue workers running.
- Scheduler writes expected log/activity after a scheduled minute.
- `storage/logs/laravel.log` has no new critical errors.
- Payment webhooks are configured only after HTTPS and real provider credentials are confirmed.
- Mail provider sends a test transactional email from production domain.
- Backup job completes and restore location is known.
