FROM php

RUN mkdir app/
WORKDIR /app

# Installation de Xdebug pour Debian a rechecker
# Configuration du php.ini indispensable.
# S'y référer.
# FROM php:7.4-cli
# RUN pecl install redis-5.1.1 \
    # && pecl install xdebug-2.8.1 \
    # && docker-php-ext-enable redis xdebug

RUN apt-get -qq update \
	&& apt-get clean \
	&& pecl install ds \
	&& docker-php-ext-enable ds

RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

COPY . .

LABEL project="backend-tic-tac-toe" \
      maintainer="Bastien Rulat <bastienrulat@gmail.com>" \
      version='0.1'

CMD php -S 0.0.0.0:80 -t public/