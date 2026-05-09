FROM php:8.3-cli AS base

RUN apt-get update && apt-get install -y \
    libgmp-dev \
    unzip \
    curl \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gmp \
        pdo_sqlite \
        mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM node:22-bookworm-slim AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/css resources/css
COPY resources/js resources/js
RUN npm run build

FROM base AS final
WORKDIR /app

COPY --from=node-build /app/public/build public/build
COPY composer.json composer.lock ./
COPY --from=base /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev \
    && rm -rf /root/.composer

COPY . .

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]
