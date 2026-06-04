FROM php:8.2-apache

# 1. Install ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. Ubah DocumentRoot Apache agar membaca folder public/ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copy seluruh kode Laravel Anda
COPY . /var/www/html

# 6. Install dependensi & Atur Izin Folder (Sangat Penting)
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache