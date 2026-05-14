FROM php:8.3-cli-alpine AS base

RUN apk add --no-cache \
        gmp-dev \
        postgresql-dev \
        unzip \
        curl \
        oniguruma-dev \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gmp \
        pdo_pgsql \
        mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM node:22-bookworm-slim AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
#tailwind.config.js postcss.config.js
COPY vite.config.js ./
COPY resources/css resources/css
COPY resources/js resources/js
COPY resources/views resources/views
RUN npm run build

FROM base AS final
WORKDIR /app

COPY --from=node-build /app/public/build public/build
COPY --from=base /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts \
    && rm -rf /root/.composer
COPY . .

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]
