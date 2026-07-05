# --- Stage 1: Build Front-end Assets ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Stage 2: Final Production Image ---
FROM php:8.2-apache

# Set Document Root for Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install System Dependencies
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

# Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    zip \
    bcmath \
    intl \
    opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy front-end build from Stage 1
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions for Laravel
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Optimizing Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port (Render uses 10000 by default, but Apache defaults to 80)
# We will use a script to change Apache port to $PORT if provided
RUN sed -i "s/80/\${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Production Entrypoint
CMD ["apache2-foreground"]
