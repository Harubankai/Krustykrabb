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

# Install PHP extensions (only the ones that exist and are needed)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    bcmath \
    mbstring \
    tokenizer \
    xml

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

# Create startup script with fresh migration and seeding
RUN echo '#!/bin/sh\n\
set -e\n\
echo "Running migrations..."\n\
php artisan migrate --force\n\
echo "Running seeders..."\n\
php artisan db:seed --force\n\
echo "Clearing cache..."\n\
php artisan config:cache\n\
echo "Starting Laravel server..."\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /app/start.sh && chmod +x /app/start.sh

CMD ["/app/start.sh"]
