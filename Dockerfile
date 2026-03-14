# Board web server: PHP 8.4 Apache (aligned with Core/API PHP 8.4).
# Rebuild after changes: docker compose build web-server
FROM php:8.4-apache

RUN apt-get update -y && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        apt-utils git unzip vim zip curl mycli \
        libcurl4-openssl-dev pkg-config libonig-dev libpq-dev iputils-ping \
    && docker-php-ext-install curl mbstring mysqli pdo pdo_mysql pdo_pgsql pgsql \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && a2enmod rewrite && service apache2 restart \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
