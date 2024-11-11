# Menggunakan PHP 8.2 dengan Apache sebagai image dasar
FROM php:8.2-apache

# Mengatur direktori kerja dalam container
WORKDIR /var/www/html

# Install dependencies yang diperlukan, termasuk netcat-openbsd
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql zip \
    && a2enmod rewrite

# Copy Composer dari image resmi Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy seluruh file aplikasi ke dalam container
COPY . .

# Salin konfigurasi Apache khusus (opsional)
COPY .docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Copy wait-for script
COPY wait-for.sh /wait-for.sh
RUN chmod +x /wait-for.sh

# Install dependencies Laravel dan set izin folder
RUN composer install \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Perintah untuk menjalankan migrasi dan seeding saat container di-start
CMD /wait-for.sh db php artisan migrate:fresh --seed && apache2-foreground
