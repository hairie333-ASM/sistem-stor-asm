# =========================================================================
# Sistem Pengurusan Stor Kerajaan (TPS AM 6.1 - AM 6.10) - ASM
# Production Dockerfile: PHP 8.4-FPM + Nginx + PostgreSQL Driver
# =========================================================================

FROM php:8.4-fpm-alpine

# Set persekitaran kerja
WORKDIR /var/www/html

# Pasang pakej sistem dan perkhidmatan Nginx & Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    libpq

# Pasang pemalam PHP rasmi (pantas, pra-bina, cegah ralat memori OOM di Render)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    mbstring \
    xml \
    bcmath \
    opcache \
    zip \
    intl \
    gd

# Konfigurasi OPcache untuk prestasi maksimum di pengeluaran
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Pasang Composer rasmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin fail definisi kebergantungan
COPY composer.json composer.lock ./

# Pasang kebergantungan tanpa dev packages untuk saiz imej minimum
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# Salin keseluruhan kod aplikasi
COPY . .

# Konfigurasi Nginx, Supervisor dan Skrip Permulaan
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Tetapkan keizinan direktori untuk keselamatan dan operasi www-data
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Port standard
EXPOSE 80 10000

# Titik mula perkhidmatan
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
