#!/usr/bin/env bash
# First-time Ubuntu 22.04/24.04 VPS bootstrap for Bahuchar Bike Care.
# Run as a sudo-capable user ONCE on a fresh server:
#   curl -fsSL ... | bash   OR
#   chmod +x deploy/bootstrap-server.sh && ./deploy/bootstrap-server.sh
#
# After this finishes, clone the app and follow deploy/DEPLOYMENT.md section 2+.

set -euo pipefail

if [[ "${EUID}" -eq 0 ]]; then
  echo "Run this script as a normal user with sudo, not as root."
  exit 1
fi

echo "==> Updating system packages"
sudo apt update && sudo apt upgrade -y

echo "==> Installing Nginx, MySQL, PHP 8.3, Supervisor, Certbot"
sudo apt install -y \
  nginx mysql-server \
  php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl \
  php8.3-zip php8.3-bcmath php8.3-cli php8.3-tokenizer \
  unzip git supervisor certbot python3-certbot-nginx curl

echo "==> Installing Node.js 20"
if ! command -v node &>/dev/null || [[ "$(node -v | cut -d. -f1 | tr -d v)" -lt 20 ]]; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
  sudo apt install -y nodejs
fi

echo "==> Installing Composer"
if ! command -v composer &>/dev/null; then
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  sudo chmod +x /usr/local/bin/composer
fi

echo "==> Creating app directory"
sudo mkdir -p /var/www/bahuchar-bike-care
sudo chown -R "$USER":www-data /var/www/bahuchar-bike-care

echo ""
echo "Bootstrap complete."
echo "Next:"
echo "  1. cd /var/www/bahuchar-bike-care"
echo "  2. git clone <your-repo-url> ."
echo "  3. Follow deploy/DEPLOYMENT.md from Database section onward"
echo "  Or use a host guide: deploy/HOSTINGER.md | deploy/DIGITALOCEAN.md | deploy/CPANEL.md"
echo ""
echo "Versions:"
php -v | head -1
node -v
composer -V
nginx -v 2>&1 | head -1
