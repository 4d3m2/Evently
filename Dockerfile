ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm-alpine AS base

# Helper script that installs PHP extensions and their system libraries
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# pdo_pgsql = PostgreSQL driver, intl = i18n/formatting, opcache = bytecode cache, zip = Composer
RUN install-php-extensions pdo_pgsql intl opcache zip

WORKDIR /app

############################################
# dev: base + development tooling
############################################
FROM base AS dev

# Match the container user to host user 
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache git unzip \
 && addgroup -g ${GROUP_ID} app \
 && adduser -D -u ${USER_ID} -G app app \
 && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

USER app
