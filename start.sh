#!/bin/sh

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (only in production/live environment)
# Use --force to avoid confirmation prompts
php artisan migrate --force

# Start Apache
apache2-foreground
