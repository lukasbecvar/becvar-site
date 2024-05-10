# becvar-site testing docker file (only-dev env)
FROM php:8.3-apache

# set default apache working directory
WORKDIR /var/www

# update package index and install necessary dependencies
RUN apt-get update && apt-get install -y \
    rpm \
    libzip-dev \
    unzip \
    libicu-dev \ 
    && docker-php-ext-install zip pdo_mysql mysqli pdo intl

# install OPCache
RUN docker-php-ext-install opcache

# enable apache rewrite module
RUN a2enmod rewrite
