#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
MAX_TRIES=30
TRIES=0

until mysql -h db -u ${DB_USERNAME} -p${DB_PASSWORD} -e "SELECT 1" > /dev/null 2>&1; do
    TRIES=$((TRIES+1))
    if [ $TRIES -gt $MAX_TRIES ]; then
        echo "Error: MySQL not available after $MAX_TRIES attempts"
        exit 1
    fi
    echo "MySQL not ready yet, waiting..."
    sleep 2
done

echo "MySQL is ready!"

# Laravel setup
cd /var/www/html

# Set correct permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key if needed
if [ "$APP_KEY" == "" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Start Apache
apache2-foreground