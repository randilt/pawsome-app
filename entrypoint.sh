#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
MAX_TRIES=30
TRIES=0

# Simple TCP connection check
until nc -z db 3306; do
    TRIES=$((TRIES+1))
    if [ $TRIES -gt $MAX_TRIES ]; then
        echo "Error: MySQL service not available after $MAX_TRIES attempts"
        echo "Continuing anyway..."
        break
    fi
    echo "MySQL service not ready yet, waiting... (Attempt $TRIES/$MAX_TRIES)"
    sleep 5
done

echo "MySQL service is up - giving it a few seconds to settle..."
sleep 10

# Laravel setup
cd /var/www/html

# Set correct permissions
chown -R www-data:www-data storage bootstrap/cache

# Generate key if needed
if [ "$APP_KEY" == "" ]; then
    php artisan key:generate
fi

# Run migrations with a retry mechanism
echo "Attempting to run migrations..."
MIGRATE_TRIES=5
for i in $(seq 1 $MIGRATE_TRIES)
do
    echo "Migration attempt $i/$MIGRATE_TRIES"
    if php artisan migrate --force; then
        echo "Migrations completed successfully!"
        break
    fi
    
    if [ $i -eq $MIGRATE_TRIES ]; then
        echo "Could not run migrations after $MIGRATE_TRIES attempts"
    else
        echo "Migration failed, retrying in 5 seconds..."
        sleep 5
    fi
done

# Run database seeder
echo "Seeding the database..."
php artisan db:seed --force

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Laravel setup complete, starting Apache..."

# Start Apache
apache2-foreground