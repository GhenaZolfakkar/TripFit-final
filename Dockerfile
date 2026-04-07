# ------------------------
# Base Image
# ------------------------
FROM php:8.2-fpm

# ------------------------
# Set working directory
# ------------------------
WORKDIR /var/www/html

# ------------------------
# Install system dependencies
# ------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    npm \
    && docker-php-ext-install intl pdo_mysql mbstring zip exif pcntl gd

# ------------------------
# Install Composer
# ------------------------
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# ------------------------
# Copy application files
# ------------------------
COPY . .

# ------------------------
# Copy production env
# ------------------------
# ------------------------
# Install PHP dependencies
# ------------------------
RUN composer install --optimize-autoloader --no-dev --no-interaction

# ------------------------
# Install Node dependencies for Vite
# ------------------------
RUN npm install && npm run build

# ------------------------
# Permissions for Laravel
# ------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ------------------------
# Expose port for Railway
# ------------------------
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}