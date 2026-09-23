#!/bin/sh
set -e

echo "=== Memulakan Sistem Pengurusan Stor Kerajaan (ASM) ==="

# 1. Konfigurasi Port Dinamik Render (Render passes $PORT, e.g. 10000)
if [ -n "$PORT" ]; then
    echo "Mengkonfigurasi Nginx untuk mendengar pada Port: $PORT"
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/conf.d/default.conf
    sed -i "s/listen \[::\]:80;/listen \[::\]:$PORT;/g" /etc/nginx/conf.d/default.conf
fi

# 2. Pastikan Direktori Storage & Cache Diberikan Keizinan Sempurna
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Kunci Aplikasi (APP_KEY)
if [ -z "$APP_KEY" ]; then
    echo "Amaran: APP_KEY tidak ditetapkan dalam Environment Variables. Menjana kunci sementara..."
    php artisan key:generate --force
fi

# 4. Pengoptimuman Prestasi Pengeluaran (Production Caching)
echo "Mengoptimumkan konfigurasi, laluan, dan paparan Blade..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 5. Migrasi & Seeding Pangkalan Data Automatik (Supabase / PostgreSQL)
if [ -n "$DATABASE_URL" ] || [ "$DB_CONNECTION" = "pgsql" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Menjalankan migrasi pangkalan data (php artisan migrate --force)..."
    php artisan migrate --force || echo "Amaran: Migrasi gagal atau pangkalan data belum bersedia."

    echo "Memeriksa dan menyemai data asas & katalog stok 981 item..."
    php artisan db:seed --force || echo "Seeding selesai atau telah wujud."
fi

echo "=== Pelayan Web Sedia Menerima Trafik ==="

# 6. Jalankan Supervisor (PHP-FPM + Nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
