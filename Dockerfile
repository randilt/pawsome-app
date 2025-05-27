FROM node:18-alpine AS node-builder

# Set working directory for Node
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install Node dependencies
RUN npm ci

# Copy source files needed for build
COPY . .

# Build assets
RUN npm run build

# PHP Stage
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

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-mongodb

# Copy application code
COPY . .

# Copy built assets from node-builder stage
COPY --from=node-builder /app/public/build /app/public/build

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