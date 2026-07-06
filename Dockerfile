# --- Stage 1: Build Front-end Assets ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Stage 2: Final Production Image ---
FROM php:8.4-apache

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Configure Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Fix Apache MPM conflict: ensure ONLY prefork is enabled for PHP module
RUN a2dismod mpm_event mpm_worker || true && \
    a2enmod mpm_prefork rewrite

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
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (dynamic for cloud platforms)
RUN sed -i "s/80/\${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Production Entrypoint
RUN chmod +x /var/www/html/start.sh
ENTRYPOINT ["/var/www/html/start.sh"]
