# Stage 1 - Build Frontend (Vite)
FROM node:20 AS frontend

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install frontend dependencies
RUN npm install --legacy-peer-deps

# Copy project files
COPY . .

# Build frontend assets
RUN npm run build


# Stage 2 - Laravel + PHP
FROM php:8.2-cli

# Install required system packages + SQLite support
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

# Copy Laravel project files
COPY . .

# Copy Vite build output
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create SQLite database file
RUN mkdir -p database && touch database/database.sqlite

# Ensure Laravel storage/cache folders exist
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Fix permissions for Laravel sessions/cache
RUN chmod -R 775 storage bootstrap/cache

# Clear all Laravel cached config/routes/views
RUN php artisan optimize:clear || true

# Expose Render port
EXPOSE 10000

# Start Laravel server for Render
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
