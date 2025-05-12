FROM php:8.2-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev

# Enable mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd

# Set the Apache document root to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install latest composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist --no-interaction

# Copy application files
COPY . .

# Generate autoloader and optimize
RUN composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 80

# Start server
# Update this line
CMD if [ ! -z "$PORT" ]; then \
        sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf && \
        sed -i "s/80/$PORT/g" /etc/apache2/ports.conf; \
    fi && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    apache2-foreground