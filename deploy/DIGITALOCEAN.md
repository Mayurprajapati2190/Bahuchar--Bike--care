# DigitalOcean Droplet — Quick deploy (Bahuchar Bike Care)

## 1. Create Droplet

1. DigitalOcean → Create → Droplets  
2. Image: **Ubuntu 24.04 LTS**  
3. Size: 1 GB RAM minimum (2 GB better)  
4. Add your SSH key  
5. Create → copy the **public IP**  
6. Domain DNS **A record** → that IP  

## 2. SSH

```bash
ssh root@YOUR_DROPLET_IP
```

Optional non-root user:

```bash
adduser deploy
usermod -aG sudo deploy
su - deploy
```

## 3. Clone + bootstrap

```bash
sudo apt update && sudo apt install -y git
sudo mkdir -p /var/www/bahuchar-bike-care
sudo chown -R $USER:www-data /var/www/bahuchar-bike-care
cd /var/www/bahuchar-bike-care
git clone https://github.com/Mayurprajapati2190/Bahuchar--Bike--care.git .
chmod +x deploy/bootstrap-server.sh
./deploy/bootstrap-server.sh
```

## 4. MySQL

```bash
sudo mysql -e "CREATE DATABASE bahuchar_bike_care CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'bahuchar'@'localhost' IDENTIFIED BY 'CHOOSE_STRONG_PASSWORD';"
sudo mysql -e "GRANT ALL PRIVILEGES ON bahuchar_bike_care.* TO 'bahuchar'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## 5. App

```bash
cd /var/www/bahuchar-bike-care
composer install --no-dev --optimize-autoloader
cp deploy/.env.production.example .env
php artisan key:generate
nano .env   # set APP_URL, DB_* password
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan optimize
php artisan storage:link --force
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 6. Nginx + SSL + workers

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/bahuchar-bike-care
sudo nano /etc/nginx/sites-available/bahuchar-bike-care   # set domain
sudo ln -sf /etc/nginx/sites-available/bahuchar-bike-care /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d yourdomain.com

sudo cp deploy/supervisor-worker.conf /etc/supervisor/conf.d/bahuchar-worker.conf
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start bahuchar-worker:*

sudo crontab -u www-data -e
# add: * * * * * cd /var/www/bahuchar-bike-care && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Verify

Open `https://yourdomain.com/up` and `/login`.

Updates later: `./deploy/deploy.sh`

Full reference: [DEPLOYMENT.md](DEPLOYMENT.md)
