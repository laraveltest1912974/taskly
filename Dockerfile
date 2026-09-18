# syntax=docker/dockerfile:1

# Production image for hosting on Render (nginx + php-fpm in one container).

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY app ./app
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources ./vendor/laravel/framework/src/Illuminate/Pagination/resources
RUN npm run build

FROM php:8.5-fpm-alpine AS app
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apk add --no-cache nginx supervisor ca-certificates \
    && install-php-extensions pdo_mysql intl zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

COPY deploy/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY deploy/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY deploy/supervisord.conf /etc/supervisord.conf
COPY deploy/nginx.conf /etc/nginx/templates/default.conf
COPY deploy/start.sh /usr/local/bin/start.sh

RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 10000
CMD ["/usr/local/bin/start.sh"]
