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

# ============================================================
# Crea siempre las subcarpetas que Laravel necesita dentro de
# storage/framework. Es seguro correr esto en cada arranque
# (mkdir -p no falla si ya existen), y evita que un volumen
# nuevo y vacío (por ejemplo, al escalar a una 2da instancia
# o al clonar el proyecto por primera vez) rompa el sistema
# con errores tipo "View path not found" o
# "Please provide a valid cache path".
# ============================================================
echo "[entrypoint] Verificando subcarpetas de storage/framework..."
mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache/data storage/framework/testing
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache

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

# ============================================================
# Render asigna el puerto dinámicamente vía $PORT. La imagen
# oficial de Nginx procesa /etc/nginx/templates/*.template
# automáticamente, pero aquí no usamos esa imagen, así que hay
# que correr envsubst a mano antes de arrancar Nginx.
# ============================================================
echo "[entrypoint] Generando configuración de Nginx para el puerto ${PORT:-10000}..."
export PORT="${PORT:-10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
rm -f /etc/nginx/conf.d/default.conf.bak 2>/dev/null || true

echo "[entrypoint] Listo. Iniciando PHP-FPM y Nginx..."
exec "$@"