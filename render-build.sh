#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "=== Menjalankan Render Build ==="
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "Mengoptimumkan cache konfigurasi, laluan, dan paparan Blade..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Menjalankan migrasi pangkalan data automatik..."
if [ -n "$DATABASE_URL" ] || [ "$DB_CONNECTION" = "pgsql" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    php artisan migrate --force || true
    php artisan db:seed --force || true
fi
