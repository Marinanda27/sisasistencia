# 1. Dependencias de PHP
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

# 2. Assets con Vite
FROM node:20 AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# 3. Imagen final
FROM dunglas/frankenphp:php8.3
RUN install-php-extensions pdo_mysql mbstring gd zip bcmath intl opcache

WORKDIR /app
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

# Tu .dockerignore excluye estas carpetas, así que hay que recrearlas
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

CMD ["sh", "-c", "frankenphp php-server --root public/ --listen :${PORT:-8080}"]