FROM php:8.1-cli

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer