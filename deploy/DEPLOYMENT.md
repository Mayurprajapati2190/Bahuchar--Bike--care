# Bahuchar Bike Care — Production Deployment Guide

**New here?** Open **[START_HERE.md](START_HERE.md)** first.

Host quick guides:

- [Hostinger VPS](HOSTINGER.md)
- [DigitalOcean](DIGITALOCEAN.md)
- [cPanel shared](CPANEL.md)

This is a **Laravel + Inertia + Vue** application. It needs **PHP 8.3+, MySQL, Nginx, queue workers, and a cron scheduler**.

## Do NOT use Netlify, Vercel, or similar static hosts

Netlify and Vercel are for static / Node frontends. This app is **Laravel (PHP) + MySQL**.

- `npm run build` only creates assets in `public/build`
- Pages, login, API, SMS, and queues still need PHP running on a server

If you connect this repo to those platforms you will see intentional fail messages from `netlify.toml` / `vercel.json`. That is expected.

**Use a PHP VPS** (or Laravel Cloud / Forge / similar) instead. Follow this guide.

Ready-made files in this folder:

| File | Purpose |
|------|---------|
| `START_HERE.md` | Pick your host and start |
| `.env.production.example` | Production environment template |
| `nginx.conf` | Nginx site config |
| `supervisor-worker.conf` | Queue worker via Supervisor |
| `bootstrap-server.sh` | First-time Ubuntu software install |
| `deploy.sh` | One-command update on the server |

---

## Requirements

- Ubuntu 22.04+ or similar Linux VPS
- PHP 8.3+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- MySQL 8+
- Node.js 20+ (build assets on the server or CI)
- Composer 2
- Nginx
- Supervisor
- Certbot (Let's Encrypt)

## 1. Server setup

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath unzip git supervisor certbot python3-certbot-nginx
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Install Composer if needed: https://getcomposer.org/download/

## 2. Database

```bash
sudo mysql -e "CREATE DATABASE bahuchar_bike_care CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'bahuchar'@'localhost' IDENTIFIED BY 'your-strong-password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON bahuchar_bike_care.* TO 'bahuchar'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## 3. Deploy application

```bash
sudo mkdir -p /var/www/bahuchar-bike-care
sudo chown -R $USER:www-data /var/www/bahuchar-bike-care
cd /var/www/bahuchar-bike-care

git clone <your-repo-url> .
composer install --no-dev --optimize-autoloader
cp deploy/.env.production.example .env
php artisan key:generate
```

Edit `.env` — set `APP_URL`, database password, mail, and MSG91 if needed.

Build frontend and run migrations:

```bash
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan optimize
php artisan view:cache
php artisan storage:link --force
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Or after the first setup, use:

```bash
chmod +x deploy/deploy.sh
./deploy/deploy.sh
```

## 4. Nginx configuration

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/bahuchar-bike-care
sudo nano /etc/nginx/sites-available/bahuchar-bike-care   # set yourdomain.com
sudo ln -s /etc/nginx/sites-available/bahuchar-bike-care /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d yourdomain.com
```

## 5. Queue worker (Supervisor)

```bash
sudo cp deploy/supervisor-worker.conf /etc/supervisor/conf.d/bahuchar-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bahuchar-worker:*
```

## 6. Laravel scheduler (cron)

SMS reminders run daily at 9:00 AM IST. Backups run monthly.

```bash
sudo crontab -u www-data -e
```

Add:

```cron
* * * * * cd /var/www/bahuchar-bike-care && php artisan schedule:run >> /dev/null 2>&1
```

Verify:

```bash
php artisan schedule:list
```

## 7. Database backups (optional extra)

Create `/usr/local/bin/backup-bahuchar-db.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/bahuchar-bike-care"
mkdir -p "$BACKUP_DIR"
mysqldump -u bahuchar -p'your-strong-password' bahuchar_bike_care | gzip > "$BACKUP_DIR/db-$(date +%F-%H%M).sql.gz"
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +14 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-bahuchar-db.sh
sudo crontab -e
```

Add daily backup at 2 AM:

```cron
0 2 * * * /usr/local/bin/backup-bahuchar-db.sh
```

## 8. Health check

Laravel exposes `/up` for uptime monitoring. Configure [UptimeRobot](https://uptimerobot.com) (free tier) to ping this endpoint.

## 9. Post-deploy smoke test

1. Open `https://yourdomain.com/up` — should return OK
2. Log in at `https://yourdomain.com/login`
3. Create a customer with a valid 10-digit mobile number
4. Add a bike and create a service record
5. Click **Complete & Send SMS** — verify SMS log (and real SMS if MSG91 is enabled)
6. Run `php artisan services:send-reminders` manually to test reminder queue
7. Install PWA on mobile via browser **Add to Home Screen**

## 10. Updating the app

```bash
cd /var/www/bahuchar-bike-care
./deploy/deploy.sh
```

Or manually:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
php artisan view:cache
sudo supervisorctl restart bahuchar-worker:*
```

## 11. Android apps (REST API)

The Laravel backend exposes a REST API at `/api/v1/` for native Android apps (staff + customer).

### API requirements

- **HTTPS** required for Play Store release builds
- **Sanctum** token auth — run migrations including `personal_access_tokens` and `customer_otps`
- **Queue worker** must run for SMS
- Clear route cache after deploy: `php artisan optimize`

### Environment variables

```env
SANCTUM_TOKEN_EXPIRATION=43200
CORS_ALLOWED_ORIGINS=https://yourdomain.com
```

### Staff API auth

```bash
curl -X POST https://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"your-password"}'
```

Use the returned `token` as `Authorization: Bearer {token}` on subsequent requests.

### Customer API auth

1. `POST /api/v1/customer/auth/request-otp` with `{ "phone": "9876543210" }`
2. `POST /api/v1/customer/auth/verify-otp` with `{ "phone": "9876543210", "code": "123456" }`

Phone must belong to an existing customer. In free SMS mode, OTP is logged to `storage/logs/laravel.log`.

### Android build

See [mobile/README.md](../mobile/README.md) for Android Studio setup, API base URL, and Play Store signing.

## 12. Production environment checklist

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://yourdomain.com`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_ENCRYPT=true`
- [ ] `QUEUE_CONNECTION=database` + Supervisor worker running
- [ ] Cron for `schedule:run`
- [ ] SSL via Certbot
- [ ] `npm run build` completed (`public/build` present)
- [ ] `/up` returns 200
