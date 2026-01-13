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

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV SERVER_NAME="damirkassen92.duckdns.org"
ENV FRANKENPHP_DOCUMENT_ROOT="/app/public"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./Caddyfile /etc/caddy/Caddyfile
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /app

COPY ./composer.json ./composer.lock ./symfony.lock ./
COPY . .

RUN chmod +x entry.sh
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative --no-scripts --no-cache --prefer-dist
