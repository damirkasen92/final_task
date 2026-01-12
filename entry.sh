#!/bin/sh
set -e;

php bin/console cache:clear --no-warmup;
php bin/console importmap:install --no-interaction;
php bin/console assets:install --no-interaction;
php bin/console asset-map:compile;
php bin/console doctrine:migrations:migrate --no-interaction;
php bin/console app:meili:create-index;

exec frankenphp run /etc/caddy/Caddyfile;
