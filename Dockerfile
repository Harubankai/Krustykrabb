# Stage 1 - Build Frontend
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Stage 2 - Laravel + PHP
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip sqlite3 libsqlite3-dev \
    libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p database && touch database/database.sqlite

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

CMD ["php-fpm"]