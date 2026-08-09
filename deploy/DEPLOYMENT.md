# Bahuchar Bike Care — Production Deployment Guide

This guide covers deploying the Laravel application to a Linux VPS with Nginx, MySQL, queue workers, scheduled SMS reminders, and HTTPS.

## Requirements

- Ubuntu 22.04+ or similar Linux VPS
- PHP 8.3+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`
- MySQL 8+
- Node.js 20+ (build assets locally or on server)
- Composer 2
- Nginx
- Supervisor
- Certbot (Let's Encrypt)

## 1. Server setup

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath unzip git supervisor certbot python3-certbot-nginx
```

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
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Bahuchar Bike Care"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bahuchar_bike_care
DB_USERNAME=bahuchar
DB_PASSWORD=your-strong-password

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

MSG91_ENABLED=true
MSG91_AUTH_KEY=your-msg91-auth-key
MSG91_CONFIRMATION_TEMPLATE_ID=your-dlt-template-id
MSG91_REMINDER_TEMPLATE_ID=your-dlt-template-id
MSG91_SHOP_PHONE="+91 XXXXX XXXXX"
```

Build frontend and run migrations:

```bash
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 4. Nginx configuration

Create `/etc/nginx/sites-available/bahuchar-bike-care`:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/bahuchar-bike-care/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and SSL:

```bash
sudo ln -s /etc/nginx/sites-available/bahuchar-bike-care /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d yourdomain.com
```

## 5. Queue worker (Supervisor)

Create `/etc/supervisor/conf.d/bahuchar-worker.conf`:

```ini
[program:bahuchar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bahuchar-bike-care/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/bahuchar-bike-care/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bahuchar-worker:*
```

## 6. Laravel scheduler (cron)

SMS reminders run daily at 9:00 AM IST via `services:send-reminders`.

```bash
sudo crontab -u www-data -e
```

Add:

```cron
* * * * * cd /var/www/bahuchar-bike-care && php artisan schedule:run >> /dev/null 2>&1
```

Verify scheduler:

```bash
php artisan schedule:list
```

## 7. Database backups

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

1. Log in at `https://yourdomain.com/login`
2. Create a customer with a valid 10-digit mobile number
3. Add a bike and create a service record
4. Click **Complete & Send SMS** — verify SMS log entry (and actual SMS if MSG91 is enabled)
5. Run `php artisan services:send-reminders` manually to test reminder queue
6. Install PWA on mobile via browser **Add to Home Screen**

## 10. Updating the app

```bash
cd /var/www/bahuchar-bike-care
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart bahuchar-worker:*
```

## 11. Android apps (REST API)

The Laravel backend exposes a REST API at `/api/v1/` for native Android apps (staff + customer).

### API requirements

- **HTTPS** required for Play Store release builds
- **Sanctum** token auth — run migrations including `personal_access_tokens` and `customer_otps`
- **Queue worker** must run for SMS (unchanged)
- Clear route cache after deploy: `php artisan route:clear && php artisan route:cache`

### Environment variables

```env
SANCTUM_TOKEN_EXPIRATION=43200
CORS_ALLOWED_ORIGINS=*
```

Restrict `CORS_ALLOWED_ORIGINS` in production if needed.

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
