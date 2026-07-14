#!/bin/sh
set -e

cd /var/www/html

# El código llega por bind-mount, así que las dependencias se instalan
# aquí (no en el build de la imagen) para que no queden ocultas por el volumen.
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "[entrypoint] Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f ".env" ]; then
    echo "[entrypoint] Copiando .env.example a .env..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64" .env; then
    echo "[entrypoint] Generando APP_KEY..."
    php artisan key:generate --force
fi

echo "[entrypoint] Esperando a que la base de datos (${DB_HOST:-postgres}) esté disponible..."
until php artisan db:show > /dev/null 2>&1; do
    sleep 2
done
echo "[entrypoint] Base de datos disponible."

echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force

if [ "${DB_SEED:-true}" = "true" ]; then
    echo "[entrypoint] Ejecutando seeders..."
    php artisan db:seed --force || true
fi

php artisan storage:link || true
php artisan config:cache

echo "[entrypoint] Listo. Iniciando php-fpm..."
exec php-fpm