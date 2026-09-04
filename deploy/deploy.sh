#!/usr/bin/env bash
# Production deploy / update script for Bahuchar Bike Care (Linux VPS).
# Usage (from project root on the server):
#   chmod +x deploy/deploy.sh
#   ./deploy/deploy.sh

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "==> Pulling latest code"
git pull --ff-only

echo "==> Installing PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installing JS dependencies and building assets"
npm ci
npm run build

echo "==> Running migrations"
php artisan migrate --force

echo "==> Caching config / routes / views"
php artisan optimize
php artisan view:cache

echo "==> Linking storage"
php artisan storage:link --force 2>/dev/null || true

echo "==> Fixing permissions"
if id www-data &>/dev/null; then
  sudo chown -R www-data:www-data storage bootstrap/cache
  sudo chmod -R 775 storage bootstrap/cache
fi

echo "==> Restarting queue worker (if supervisor is configured)"
if command -v supervisorctl &>/dev/null; then
  sudo supervisorctl restart bahuchar-worker:* 2>/dev/null || true
fi

echo "==> Done. Health check: curl -fsS \"\${APP_URL:-http://127.0.0.1}/up\""
