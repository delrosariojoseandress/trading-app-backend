FROM php:8.3-apache

# =====================================
# SYSTEM DEPENDENCIES
# =====================================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    netcat-openbsd \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip

# =====================================
# ENABLE APACHE REWRITE (Laravel routing)
# =====================================
RUN a2enmod rewrite

# =====================================
# SET LARAVEL PUBLIC FOLDER
# =====================================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# =====================================
# WORKDIR
# =====================================
WORKDIR /var/www/html

# =====================================
# COPY APPLICATION CODE
# =====================================
COPY . .

# =====================================
# INSTALL COMPOSER
# =====================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =====================================
# INSTALL PHP DEPENDENCIES
# =====================================
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# =====================================
# PERMISSIONS (IMPORTANT FOR LARAVEL)
# =====================================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# =====================================
# ENTRYPOINT SCRIPT
# =====================================
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# =====================================
# EXPOSE PORT
# =====================================
EXPOSE 80

# =====================================
# START CONTAINER
# =====================================
CMD ["/entrypoint.sh"]