FROM php:8.3-fpm-alpine AS base

# Install dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip gd bcmath pcntl

# Set working directory
WORKDIR /var/www/html

# Configure nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configure supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

FROM base AS build

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Clear cache
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

FROM base AS final

# Copy optimized application from build stage
COPY --from=build /var/www/html /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]