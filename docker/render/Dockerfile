FROM php:8.4-fpm

RUN sed -i 's|http://deb.debian.org|https://deb.debian.org|g; s|http://security.debian.org|https://security.debian.org|g' /etc/apt/sources.list.d/debian.sources

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libpq-dev libsqlite3-dev \
    zip unzip nginx supervisor gettext-base postgresql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN rm -f /etc/nginx/sites-enabled/default

WORKDIR /var/www/html
COPY backend/ /var/www/html/

COPY docker/render/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/render/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh

# Limpia BOM y CRLF de TODOS los archivos de arranque (no solo el entrypoint)
RUN for f in /usr/local/bin/entrypoint.sh /etc/supervisor/conf.d/supervisord.conf /etc/nginx/templates/default.conf.template; do \
        sed -i '1s/^\xEF\xBB\xBF//' "$f" && sed -i 's/\r$//' "$f"; \
    done \
    && chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000
ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]