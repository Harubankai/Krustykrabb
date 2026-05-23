# Stage 1 - Build Frontend (Vite)
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install --legacy-peer-deps

COPY . .

RUN npm run build


# Stage 2 - Laravel + PHP
FROM php:8.2-cli

# Install system dependencies + SQLite support
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create SQLite database
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite

# Copy Vite build output
COPY --from=frontend /app/public/build ./public/build

# Ensure Laravel required folders exist
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Fix permissions (VERY important for 500 errors)
RUN chmod -R 775 storage bootstrap/cache database

# Clear and optimize Laravel
RUN php artisan optimize:clear || true

# IMPORTANT: run migrations (fixes login/register 500)
RUN php artisan migrate --force || true

# Expose Render port
EXPOSE 10000

# Start Laravel server (Render compatible)
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
