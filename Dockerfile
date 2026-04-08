FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev libonig-dev libxml2-dev libicu-dev \
    npm \
    && docker-php-ext-install intl pdo_mysql zip bcmath opcache \
    && a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Set document root to Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 🔥 FIX PORT (VERY IMPORTANT)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend
RUN npm install && npm run build

# Laravel cache
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["apache2-foreground"]