FROM php:7-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql && pecl install apcu && docker-php-ext-enable apcu

RUN docker-php-ext-install opcache && docker-php-ext-enable opcache

RUN apt update && apt install -y zlib1g-dev libpng-dev g++ libicu-dev libpq-dev libzip-dev zip zlib1g-dev

RUN docker-php-ext-install gd && docker-php-ext-enable gd

RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

RUN chmod -R a+r /var/www/html/

RUN composer install

EXPOSE 80
