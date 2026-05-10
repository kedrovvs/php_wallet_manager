#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

php artisan package:discover --ansi
php artisan migrate --force

exec "$@"
