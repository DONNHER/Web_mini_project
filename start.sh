#!/bin/sh

# Ensure storage directories exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions again just in case
chown -R www-data:www-data storage bootstrap/cache

# Create storage link
php artisan storage:link --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start Apache
exec apache2-foreground
