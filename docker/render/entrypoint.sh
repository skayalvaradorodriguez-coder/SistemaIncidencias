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

echo "[entrypoint] Verificando subcarpetas de storage/framework..."
mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache/data
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache

echo "[entrypoint] Esperando a que PostgreSQL acepte conexiones..."
ATTEMPTS=0
until php -r "new PDO('pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
    ATTEMPTS=$((ATTEMPTS+1))
    if [ "$ATTEMPTS" -ge 30 ]; then
        echo "[entrypoint] ERROR: PostgreSQL no respondió tras 30 intentos."
        break
    fi
    echo "[entrypoint] PostgreSQL aún no responde (intento $ATTEMPTS)..."
    sleep 2
done

echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force

if [ "${DB_SEED:-false}" = "true" ]; then
    echo "[entrypoint] Ejecutando seeders..."
    php artisan db:seed --force
fi

php artisan storage:link >/dev/null 2>&1 || true
php artisan optimize:clear
php artisan config:cache

echo "[entrypoint] Generando configuracion de Nginx para el puerto ${PORT:-10000}..."
export PORT="${PORT:-10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

echo "[entrypoint] Listo. Iniciando supervisord (PHP-FPM + Nginx)..."
exec "$@"