FROM php:7.4-cli

RUN docker-php-ext-install pcntl

WORKDIR /usr/src/myapp

COPY . /usr/src/myapp

EXPOSE 8282

CMD ["php", "wsserver.php", "start"]