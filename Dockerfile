FROM php:7.4-alpine as php

RUN adduser --disabled-password user
ENV USER user

# composerの導入
COPY --from=composer /usr/bin/composer /usr/bin/composer

# common
USER user
WORKDIR /var/www/app