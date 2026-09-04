# Hostinger VPS — Quick deploy (Bahuchar Bike Care)

Do **not** use Hostinger “static site” or Netlify/Vercel. Use a **VPS** plan with Ubuntu + root/SSH access.

## 1. Create VPS

1. Hostinger → VPS → Ubuntu 22.04 or 24.04  
2. Note the **IP** and root password / SSH key  
3. Point your domain **A record** to that IP (DNS → your domain)

## 2. SSH into the server

```bash
ssh root@YOUR_SERVER_IP
```

Create a deploy user (recommended):

```bash
adduser deploy
usermod -aG sudo deploy
su - deploy
```

## 3. Bootstrap software

```bash
# After cloning once, OR paste bootstrap from the repo:
# From your PC (with git), you can scp the script, or clone first then run:

sudo apt update && sudo apt install -y git
sudo mkdir -p /var/www/bahuchar-bike-care
sudo chown -R $USER:www-data /var/www/bahuchar-bike-care
cd /var/www/bahuchar-bike-care
git clone https://github.com/Mayurprajapati2190/Bahuchar--Bike--care.git .
chmod +x deploy/bootstrap-server.sh
./deploy/bootstrap-server.sh
```

## 4. Database

```bash
sudo mysql
```

In MySQL:

```sql
CREATE DATABASE bahuchar_bike_care CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bahuchar'@'localhost' IDENTIFIED BY 'CHOOSE_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON bahuchar_bike_care.* TO 'bahuchar'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. App .env + build

```bash
cd /var/www/bahuchar-bike-care
composer install --no-dev --optimize-autoloader
cp deploy/.env.production.example .env
php artisan key:generate
nano .env
```

Set at least:

```env
APP_URL=https://yourdomain.com
DB_DATABASE=bahuchar_bike_care
DB_USERNAME=bahuchar
DB_PASSWORD=CHOOSE_STRONG_PASSWORD
```

Then:

```bash
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan optimize
php artisan storage:link --force
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 6. Nginx + SSL

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/bahuchar-bike-care
sudo nano /etc/nginx/sites-available/bahuchar-bike-care
# change yourdomain.com → your real domain
sudo ln -sf /etc/nginx/sites-available/bahuchar-bike-care /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d yourdomain.com
```

## 7. Queue + cron

```bash
sudo cp deploy/supervisor-worker.conf /etc/supervisor/conf.d/bahuchar-worker.conf
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start bahuchar-worker:*

sudo crontab -u www-data -e
```

Add:

```cron
* * * * * cd /var/www/bahuchar-bike-care && php artisan schedule:run >> /dev/null 2>&1
```

## 8. Verify

- https://yourdomain.com/up  
- https://yourdomain.com/login (admin from AdminUserSeeder)

## Later updates

```bash
cd /var/www/bahuchar-bike-care
./deploy/deploy.sh
```

Full reference: [DEPLOYMENT.md](DEPLOYMENT.md)
