#!/bin/sh
set -e
cd /var/www/html

if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "[entrypoint] Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f ".env" ]; then
    echo "[entrypoint] Copiando .env.example a .env..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "[entrypoint] Generando APP_KEY..."
    php artisan key:generate --force
fi

echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force

if [ "${DB_SEED:-false}" = "true" ]; then
    echo "[entrypoint] Ejecutando seeders..."
    php artisan db:seed --force
fi

php artisan storage:link >/dev/null 2>&1 || true
php artisan optimize:clear
php artisan config:cache

export PORT="${PORT:-10000}"
echo "[entrypoint] Generando configuracion de Nginx con PORT=$PORT..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

echo "[entrypoint] Listo. Iniciando supervisord (nginx + php-fpm)..."
exec "$@"
