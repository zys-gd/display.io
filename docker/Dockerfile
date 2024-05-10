FROM php:8.3-rc-cli-alpine as base

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions  \
    bcmath  \
    intl  \
    zip  \
    @composer \
    xdebug

RUN apk --update add \
        curl \
        libzip-dev \
        unzip \
        wget

WORKDIR /var/www
