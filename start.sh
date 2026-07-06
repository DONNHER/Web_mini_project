#!/bin/sh

# Fail on error
set -e

echo "🚀 Initializing LendingSystem..."

# --- DYNAMIC PORT CONFIGURATION ---
# Railway provides a dynamic $PORT variable. We must update Apache to listen on it.
export PORT=${PORT:-8080}
echo "🌐 Configuring Apache to listen on port $PORT..."
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# --- APACHE MPM FIX ---
echo "🔧 Enforcing Apache mpm_prefork..."
rm -f /etc/apache2/mods-enabled/mpm_event.*
rm -f /etc/apache2/mods-enabled/mpm_worker.*
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# --- LARAVEL INITIALIZATION ---
echo "📂 Setting up storage and permissions..."
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link --force

echo "⚡ Optimizing application performance..."
rm -rf storage/framework/views/*.php
php artisan config:cache
php artisan route:cache
php artisan view:cache || echo "Blade caching skipped."

echo "📊 Running database migrations..."
php artisan migrate --force

echo "✅ Deployment successful. Starting Apache..."

# Start Apache
exec apache2-foreground
