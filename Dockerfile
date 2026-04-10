FROM php:8.2-cli

# Install dependencies + GD extension
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080