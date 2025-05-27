FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (ignore platform requirements for now)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-mongodb

# Copy application code
COPY . .

# Now run composer again to ensure everything is properly installed
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
RUN chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Create required directories
RUN mkdir -p /app/storage/logs
RUN mkdir -p /app/bootstrap/cache

# Clear any cached config that might have wrong env values
RUN rm -rf /app/bootstrap/cache/*.php

# DON'T cache config in Docker build - let it read env vars at runtime
# RUN php artisan config:cache || echo "Config cache failed, continuing..."
# RUN php artisan route:cache || echo "Route cache failed, continuing..."
# RUN php artisan view:cache || echo "View cache failed, continuing..."

# Create a startup script
RUN echo '#!/bin/bash\n\
# Clear any cached config\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
# Start the server\n\
php artisan serve --host=0.0.0.0 --port=$PORT\n\
' > /app/start.sh && chmod +x /app/start.sh

# Expose port
EXPOSE 8080

# Use the startup script
CMD ["/app/start.sh"]