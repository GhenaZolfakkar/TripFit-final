FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libzip-dev zip

RUN docker-php-ext-install intl pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD php -S 0.0.0.0:$PORT -t public