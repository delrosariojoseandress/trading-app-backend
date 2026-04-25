FROM php:8.3-apache

# =========================
# SYSTEM DEPENDENCIES
# =========================
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Enable Apache rewrite (Laravel routing)
RUN a2enmod rewrite

# =========================
# APACHE CONFIG (IMPORTANT)
# =========================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# =========================
# WORKDIR
# =========================
WORKDIR /var/www/html

# Copy project files
COPY . .

# =========================
# COMPOSER
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# =========================
# PERMISSIONS
# =========================
RUN chown -R www-data:www-data /var/www/html

# =========================
# OPTIONAL: RUN MIGRATIONS ON START
# =========================
CMD php artisan migrate --force && apache2-foreground

# =========================
# EXPOSE PORT
# =========================
EXPOSE 80