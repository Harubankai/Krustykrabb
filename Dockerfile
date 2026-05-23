# Stage 1 - Build Frontend (Vite)
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install --legacy-peer-deps

COPY . .

RUN npm run build


# Stage 2 - Laravel + PHP
FROM php:8.2-cli

# Install system dependencies + PostgreSQL support
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy Vite build output
COPY --from=frontend /app/public/build ./public/build

# Ensure Laravel required folders exist
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Fix permissions (IMPORTANT for 500 errors)
RUN chmod -R 775 storage bootstrap/cache

# Clear Laravel cache
RUN php artisan optimize:clear || true

# Run migrations (POSTGRESQL)
RUN php artisan migrate --force || true

# Expose Render port
EXPOSE 10000

# Start Laravel server for Render
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
