FROM php:8.3-apache

# update package index and install necessary dependencies
RUN apt-get update && apt-get install -y \
    rpm \ 
    libzip-dev \
    unzip \ 
    && docker-php-ext-install zip pdo_mysql mysqli pdo

# install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# install OPCache
RUN docker-php-ext-install opcache

# install APCu
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# enable apache rewrite module
RUN a2enmod rewrite
