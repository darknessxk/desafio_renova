FROM trafex/php-nginx:latest

USER root

RUN apk add --no-cache php83-pecl-xdebug \
    php83-ctype \
    php83-pdo_mysql \
    php83-mbstring

# RUN wget https://gist.githubusercontent.com/darknessxk/6e3796f8b369f9136b549df404bc7f15/raw/306a2a6f3a6f82de9c500a5d5028238a70232850/xdebug.ini -O ${PHP_INI_DIR}/conf.d/xdebug.ini

USER nobody

# HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/api/health-check

COPY --from=composer /usr/bin/composer /usr/bin/composer
