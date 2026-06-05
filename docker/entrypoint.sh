#!/bin/sh
set -e

echo "==> Waiting for MySQL to be ready..."
until php -r "new PDO('mysql:host=db;port=3306;dbname=task_management', 'taskuser', 'secret');" 2>/dev/null; do
  echo "    MySQL not ready yet, retrying in 3s..."
  sleep 3
done
echo "==> MySQL is ready."

echo "==> Clearing stale caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "==> Generating app key (if not set)..."
php artisan key:generate --no-interaction --force

echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Seeding database..."
php artisan db:seed --force --no-interaction

echo "==> Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "==> Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "==> Starting PHP-FPM..."
exec php-fpm
