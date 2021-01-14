FROM php

RUN mkdir app/
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

COPY . .

LABEL project="backend-tic-tac-toe" \
      maintainer="Bastien Rulat <bastienrulat@gmail.com>" \
      version='0.1'

CMD php -S 0.0.0.0:80 -t public/