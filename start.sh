#!/bin/sh

# Fail on error
set -e

echo "🚀 Initializing LendingSystem..."

# --- FINAL APACHE MPM CHECK ---
# Manually remove the conflicting symlinks if they exist
rm -f /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf

# Ensure prefork is the only one active
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# Ensure storage setup
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link --force

# Cleanup & Cache
rm -rf storage/framework/views/*.php
php artisan config:cache
php artisan route:cache
php artisan view:cache || echo "Blade caching skipped."

# Migrations
php artisan migrate --force

echo "✅ Boot sequence complete. Starting Apache..."

# Start Apache
exec apache2-foreground
