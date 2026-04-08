# ------------------------
# Base Image with Apache
# ------------------------
FROM php:8.2-apache

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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_mysql mbstring zip exif pcntl gd \
    && a2enmod rewrite

# ------------------------
# Install Composer
# ------------------------
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# ------------------------
# Copy application files
# ------------------------
COPY . .

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
# Expose port for Apache
# ------------------------
EXPOSE 8080

# ------------------------
# Configure Apache to listen on 8080
# ------------------------
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-available/000-default.conf

# ------------------------
# Start Apache
# ------------------------
CMD ["apache2-foreground"]