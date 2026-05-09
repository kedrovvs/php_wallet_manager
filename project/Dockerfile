FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

COPY . .

RUN chmod -R 775 storage bootstrap/cache database

EXPOSE 8000

CMD ["sh", "-c", "touch database/database.sqlite; php artisan migrate --force; php artisan serve --host=0.0.0.0 --port=8000"]
