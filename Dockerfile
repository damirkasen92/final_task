FROM dunglas/frankenphp:php8.4-alpine

RUN install-php-extensions \
    intl \
    pdo_pgsql \
    zip \
    opcache \
    apcu

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV SERVER_NAME=":8080"
ENV FRANKENPHP_DOCUMENT_ROOT="/app/public"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./Caddyfile /etc/caddy/Caddyfile
COPY ./opcache.ini $PHP_INI_DIR/conf.d/opcache.ini

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

WORKDIR /app

VOLUME /app/var/

COPY ./composer.json ./composer.lock ./symfony.lock ./
COPY . .

RUN composer install \
    --no-cache \ 
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-progress

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

RUN set -eux; \
    mkdir -p var/cache var/log; \
    chmod -R 777 var;

RUN chmod +x entry.sh

EXPOSE 8080

RUN set -e; \
    php bin/console cache:clear --no-warmup; \
    php bin/console cache:warmup; \
    php bin/console importmap:install --no-interaction; \
    php bin/console assets:install --no-interaction; \
    php bin/console asset-map:compile; \
    php bin/console doctrine:migrations:migrate --no-interaction;

ENTRYPOINT [ "/app/entry.sh" ]