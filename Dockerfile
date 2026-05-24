FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    npm \
    oniguruma-dev \
    libxml2-dev \
    autoconf \
    automake \
    build-base \
    postgresql-client \
    postgresql-dev

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN npm install && npm run build

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

RUN cat > /app/entrypoint.sh << 'EOF'
#!/bin/sh
set -e

echo "Clearing cache..."
php artisan optimize:clear || true

echo "Running migrations..."
php artisan migrate --force || true

echo "Caching config..."
php artisan config:cache || true

echo "Starting Laravel..."
php artisan serve --host=0.0.0.0 --port=8000
EOF

RUN chmod +x /app/entrypoint.sh

CMD ["/app/entrypoint.sh"]
