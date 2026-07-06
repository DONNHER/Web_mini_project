# --- Stage 1: Build Front-end Assets ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
# Clean install for version sync
RUN npm ci || npm install
COPY . .
# Explicit build
RUN npm run build

# --- Stage 2: Final Production Image ---
FROM php:8.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# 1. Install System Dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    zip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip bcmath intl opcache

# 3. Configure Apache Modules (Disable conflicting MPMs)
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 4. Configure Apache Document Root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 6. Asset Synchronization (CRITICAL FIX)
# Clear any existing local build files before copying from the builder stage
RUN rm -rf public/build
COPY --from=frontend-builder /app/public/build ./public/build
RUN chmod -R 755 public/build

# 7. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 8. Set final permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build

# Production Entrypoint
RUN chmod +x /var/www/html/start.sh
ENTRYPOINT ["/var/www/html/start.sh"]
