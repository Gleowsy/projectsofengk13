FROM php:8.2-apache

# 1. Install ekstensi sistem yang dibutuhkan untuk PostgreSQL dan ZIP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    zip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Aktifkan URL Rewrite Apache (Wajib untuk Laravel)
RUN a2enmod rewrite

# 3. Ubah DocumentRoot Apache agar langsung membaca folder public/ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Pindahkan semua file proyek Anda ke dalam server
WORKDIR /var/www/html
COPY . .

# 5. Install Composer dan dependensi Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. Berikan izin baca-tulis ke folder penyimpanan Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Sesuaikan port Apache dengan port dinamis dari sistem Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 8. Otomatis jalankan migrasi database lalu hidupkan web server
CMD php artisan migrate --force && apache2-foreground