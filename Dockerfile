FROM dunglas/frankenphp:php8.4-alpine

RUN install-php-extensions \
    intl \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    zip \
    opcache \
    apcu \
    curl \
    fileinfo \
    gd \
    mbstring \
    openssl \
    xsl

ENV SERVER_NAME=":80"
ENV FRANKENPHP_DOCUMENT_ROOT="/app/public"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./Caddyfile /etc/caddy/Caddyfile
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini

RUN if [ "$APP_ENV" = "prod" ]; then \
    cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini; \
    else \
    cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini; \
    fi

WORKDIR /app

COPY ./composer.json ./composer.lock ./symfony.lock ./

COPY . .

RUN if [ "$APP_ENV" = "prod" ]; then \
    echo "Запуск в продакшн-режиме"; \
    composer install --no-dev --optimize-autoloader --classmap-authoritative --no-scripts; \
    else \
    echo "Запуск в dev-режиме"; \
    composer install --optimize-autoloader; \
    fi
