FROM php:7.2-apache
RUN mkdir -p /var/www/myserver/html
RUN apt-get update -y && apt-get install -y apache2-utils libaprutil1-dbd-mysql
RUN a2enmod authn_dbd
RUN docker-php-ext-install mysqli
RUN pecl install redis && docker-php-ext-enable redis
