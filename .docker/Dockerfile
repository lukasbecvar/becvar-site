# use basic apache with php_mod image
FROM php:8.3-apache

# set default apache working directory
WORKDIR /var/www

# copy project files to container
# COPY . /var/www/

# update packages and install necessary dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libfreetype6-dev \ 
    libjpeg62-turbo-dev \
    libpng-dev \
    rpm

# install necessary PHP extensions
RUN docker-php-ext-install pdo_mysql intl

# install phpGD
RUN docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd

# install OPCache
RUN docker-php-ext-install opcache
