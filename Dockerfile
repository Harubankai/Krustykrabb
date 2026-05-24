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
    mysql-client \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    mbstring \
    tokenizer \
    xml

# Install GD support
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Install frontend dependencies and build Vite
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Expose Render port
EXPOSE 8000

# Startup script
RUN echo '#!/bin/sh\n\
set -e\n\
echo "Clearing cache..."\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
echo "Running migrations..."\n\
php artisan migrate --force\n\
echo "Starting Laravel server..."\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /app/start.sh && chmod +x /app/start.sh

CMD ["/app/start.sh"]
