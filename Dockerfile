FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    npm \
    postgresql-dev \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    bcmath \
    ctype \
    json \
    mbstring \
    openssl \
    tokenizer \
    xml \
    curl

# Install GD with freetype and jpeg support
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Install npm dependencies and build Vite
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 8000

# Create startup script
RUN echo '#!/bin/sh\n\
php artisan config:cache\n\
php artisan migrate --force\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /app/start.sh && chmod +x /app/start.sh

CMD ["/app/start.sh"]
