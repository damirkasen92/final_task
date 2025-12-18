#!/bin/sh
set -e;

exec frankenphp run /etc/caddy/Caddyfile;