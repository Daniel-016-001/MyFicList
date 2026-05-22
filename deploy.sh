#!/usr/bin/env bash
set -euo pipefail

# Ajusta estas variables si tu ruta o rama son distintas
APP_DIR="/var/www/myficlist"
BRANCH="main"

echo "Deploying branch $BRANCH to $APP_DIR"

cd "$APP_DIR"

# Asegúrate de que no haya cambios locales
git fetch --all
git checkout "$BRANCH"
git reset --hard origin/$BRANCH

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajusta permisos (user www-data es común en Ubuntu/nginx)
sudo chown -R www-data:www-data storage bootstrap/cache || true

# Reiniciar servicios (ajusta la versión de php-fpm si hace falta)
sudo systemctl restart nginx || true
sudo systemctl restart php8.1-fpm || sudo systemctl restart php-fpm || true

echo "Deploy completed."
