# ------------------------
# Base Image with Apache
# ------------------------
FROM php:8.2-apache

# ------------------------
# Install dependencies
# ------------------------
RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev libonig-dev libxml2-dev libicu-dev \
    npm \
    && docker-php-ext-install intl pdo_mysql zip bcmath opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# ------------------------
# Install Composer
# ------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ------------------------
# Set working directory
# ------------------------
WORKDIR /var/www/html

# ------------------------
# Copy project
# ------------------------
COPY . .

# ------------------------
# Set Apache Document Root to /public
# ------------------------
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# ------------------------
# Install PHP dependencies
# ------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ------------------------
# Build frontend (Vite)
# ------------------------
RUN npm install && npm run build

# ------------------------
# Laravel optimization
# ------------------------
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# ------------------------
# Permissions
# ------------------------
RUN chmod -R 775 storage bootstrap/cache

# ------------------------
# Railway requires dynamic port
# ------------------------
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# ------------------------
# Start Apache
# ------------------------
CMD ["apache2-foreground"]