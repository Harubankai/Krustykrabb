FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git curl unzip sqlite3 libsqlite3-dev \
    libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# SQLite setup
RUN mkdir -p database \
 && touch database/database.sqlite \
 && chmod 664 database/database.sqlite

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Expose Render port
EXPOSE 10000

# Start both services
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
