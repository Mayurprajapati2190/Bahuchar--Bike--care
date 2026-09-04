# cPanel / shared hosting — Quick deploy (Bahuchar Bike Care)

Use this only if your host has **PHP 8.3+**, **MySQL**, **Composer**, **SSH** (or Terminal), and you can set the document root to `public/`.

Static hosts (Netlify, Vercel) will **not** work.

## Requirements checklist

- [ ] PHP 8.3+ (enable extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `zip`)
- [ ] MySQL database created in cPanel  
- [ ] SSH or Terminal access  
- [ ] Ability to set document root to `/public`  
- [ ] Cron jobs  
- [ ] Node.js (or build assets on your PC and upload `public/build`)

Queue workers on shared hosting are limited. Prefer a VPS if SMS queues are important. On cPanel you can try a cron that runs `queue:work --stop-when-empty` every minute.

## 1. Create database (cPanel)

1. MySQL Databases → create database `bahuchar_bike_care`  
2. Create user + strong password  
3. Add user to database with ALL privileges  
4. Note host (often `localhost`)

## 2. Upload code

**Option A — Git (if available):**

```bash
cd ~/ 
git clone https://github.com/Mayurprajapati2190/Bahuchar--Bike--care.git bahuchar-bike-care
cd bahuchar-bike-care
```

**Option B — Upload ZIP** of the project via File Manager, then extract.

## 3. Document root

In cPanel → Domains / Subdomains:

- Document root = `bahuchar-bike-care/public`  
  (must end with `/public`, not the project root)

## 4. Install

```bash
cd ~/bahuchar-bike-care
composer install --no-dev --optimize-autoloader
cp deploy/.env.production.example .env
php artisan key:generate
nano .env
```

Set:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_HOST=localhost
DB_DATABASE=cpanel_db_name
DB_USERNAME=cpanel_db_user
DB_PASSWORD=cpanel_db_password
QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
```

Build assets:

```bash
# If Node is available on host:
npm ci && npm run build

# OR on your Windows PC in the project folder:
# npm ci && npm run build
# then upload the public/build folder via FTP/File Manager
```

```bash
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan optimize
php artisan storage:link --force
chmod -R 775 storage bootstrap/cache
```

## 5. Cron (cPanel → Cron Jobs)

Every minute:

```bash
cd /home/YOUR_CPANEL_USER/bahuchar-bike-care && php artisan schedule:run >> /dev/null 2>&1
```

Optional queue drain every minute:

```bash
cd /home/YOUR_CPANEL_USER/bahuchar-bike-care && php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```

## 6. SSL

cPanel → SSL/TLS Status → AutoSSL / Let’s Encrypt for your domain.

## 7. Verify

- https://yourdomain.com/up  
- https://yourdomain.com/login  

If you get 500 errors: check `storage/logs/laravel.log` and that document root is `public/`.

**Recommendation:** For a reliable garage app (SMS, queues, PWA), use a VPS — see [HOSTINGER.md](HOSTINGER.md) or [DIGITALOCEAN.md](DIGITALOCEAN.md).
