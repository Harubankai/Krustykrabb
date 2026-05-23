# Stage 1 - Build Frontend (Vite)
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install --legacy-peer-deps

COPY . .

RUN npm run build


# Stage 2 - Laravel + PHP
FROM php:8.2-fpm

# Install dependencies (SQLite support included)
RUN apt-get update && apt-get install -y \
    git curl unzip sqlite3 libsqlite3-dev \
    libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Copy Vite build output
COPY --from=frontend /app/public/build ./public/build

# Install PHP packages
RUN composer install --no-dev --optimize-autoloader

# Create SQLite database file
RUN mkdir -p database && touch database/database.sqlite

# Optional Laravel cache clear (don't fail build if unavailable)
RUN php artisan config:clear || true && \
    php artisan route:clear || true && \
    php artisan view:clear || true

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=$PORT"]
