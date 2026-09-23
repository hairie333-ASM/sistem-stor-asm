#!/bin/sh
set -e

echo "=== Memulakan Sistem Pengurusan Stor Kerajaan (ASM) ==="

# 1. Konfigurasi Port Dinamik Render (Render passes $PORT, e.g. 10000)
if [ -n "$PORT" ]; then
    echo "Mengkonfigurasi Nginx untuk mendengar pada Port: $PORT"
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/http.d/default.conf || true
fi

# 2. Sediakan fail persekitaran .env jika tiada
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# 3. Pastikan Direktori Storage & Cache Diberikan Keizinan Sempurna
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Bersihkan sebarang cache bootstrap lapuk & jana penemuan pakej pengeluaran
rm -f /var/www/html/bootstrap/cache/*.php
php artisan package:discover --ansi || true

# 5. Kunci Aplikasi (APP_KEY)
if [ -z "$APP_KEY" ]; then
    echo "Menjana kunci aplikasi APP_KEY..."
    php artisan key:generate --force || true
fi

# 6. Kosongkan cache konfigurasi dan laluan sebelum memulakan migrasi
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 7. Migrasi & Seeding Pangkalan Data Automatik (Supabase / PostgreSQL / SQLite)
if [ -n "$DATABASE_URL" ] || [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Menjalankan migrasi pangkalan data PostgreSQL/Supabase..."
    php artisan migrate --force || echo "Amaran: Migrasi PostgreSQL gagal. Sila semak sambungan Supabase."

    echo "Memeriksa dan menyemai data asas & katalog stok 981 item..."
    php artisan db:seed --force || echo "Seeding selesai atau telah wujud."
else
    # Fallback ke SQLite jika belum sambung ke PostgreSQL agar pelayan tidak crash
    echo "Menggunakan pangkalan data setempat SQLite..."
    touch /var/www/html/database/database.sqlite
    php artisan migrate --force || true
    php artisan db:seed --force || true
fi

# 8. Pengoptimuman Prestasi Pengeluaran (Production Caching)
echo "Mengoptimumkan konfigurasi, laluan, dan paparan Blade..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Pelayan Web Sedia Menerima Trafik ==="

# 9. Jalankan Supervisor (PHP-FPM + Nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
