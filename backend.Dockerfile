FROM trafex/php-nginx:latest

USER root

RUN apk add --no-cache php83-pecl-xdebug

COPY docker/backend/php/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini

USE nobody

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-interaction --no-progress

