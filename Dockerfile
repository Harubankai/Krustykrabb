FROM php:8.2-fpm-alpine

# Install system dependencies and build tools
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
    postgresql-client \
    oniguruma-dev \
    libxml2-dev \
    autoconf \
    automake \
    build-base

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
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

# Create entrypoint script with database wait logic
RUN cat > /app/entrypoint.sh << 'EOF'
#!/bin/sh
set -e

echo "Waiting for database connection..."
max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
    if php artisan tinker --execute "exit" 2>/dev/null; then
        echo "Database is ready!"
        break
    fi
    echo "Attempt $attempt/$max_attempts: Database not ready, retrying..."
    attempt=$((attempt + 1))
    sleep 2
done

if [ $attempt -gt $max_attempts ]; then
    echo "Database connection failed after $max_attempts attempts"
    exit 1
fi

echo "Running migrations..."
php artisan migrate --force || true

echo "Running seeders..."
php artisan db:seed --force || true

echo "Clearing cache..."
php artisan config:cache

echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000
EOF

RUN chmod +x /app/entrypoint.sh

ENTRYPOINT ["/app/entrypoint.sh"]
