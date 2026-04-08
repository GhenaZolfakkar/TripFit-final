FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev libonig-dev libxml2-dev libicu-dev \
    npm \
    && docker-php-ext-install intl pdo_mysql zip bcmath opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend
RUN npm install && npm run build

# Laravel cache
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# ✅ THIS IS THE IMPORTANT PART
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT}