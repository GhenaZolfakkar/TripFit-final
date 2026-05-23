FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-install intl zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN php artisan config:clear
RUN php artisan cache:clear
RUN php artisan view:clear

RUN php artisan storage:link || true
RUN php artisan filament:assets || true

EXPOSE 8080

CMD php artisan config:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}