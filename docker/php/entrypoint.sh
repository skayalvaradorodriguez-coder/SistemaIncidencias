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

echo "[entrypoint] PostgreSQL listo según el healthcheck de Docker."

echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force

if [ "${DB_SEED:-false}" = "true" ]; then
    echo "[entrypoint] Ejecutando seeders..."
    php artisan db:seed --force
fi

php artisan storage:link >/dev/null 2>&1 || true
php artisan optimize:clear
php artisan config:cache

echo "[entrypoint] Listo. Iniciando PHP-FPM..."
exec "$@"