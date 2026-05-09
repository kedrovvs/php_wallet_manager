#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

DB_FILE=${DB_DATABASE:-database/database.sqlite}
DB_DIR=$(dirname "$DB_FILE")
if [ ! -d "$DB_DIR" ]; then
    mkdir -p "$DB_DIR"
fi
if [ ! -f "$DB_FILE" ]; then
    touch "$DB_FILE"
fi

php artisan migrate --force

exec "$@"
