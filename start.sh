#!/bin/sh

# Fail on error
set -e

echo "🚀 Starting LendingSystem Infrastructure..."

# Ensure storage directories exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chown -R www-data:www-data storage bootstrap/cache

# Create storage link
php artisan storage:link --force

# Clear old view artifacts to prevent "book-card" phantom errors
echo "🧹 Cleaning legacy view artifacts..."
rm -rf storage/framework/views/*.php

# Cache configurations for production speed
echo "⚡ Optimizing system performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache || { echo "❌ Blade caching failed. Proceeding anyway..."; }

# Run migrations
echo "📊 Synchronizing database schema..."
php artisan migrate --force

echo "✅ System ready. Starting Apache..."

# Start Apache
exec apache2-foreground
