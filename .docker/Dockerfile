# use basic apache with php_mod image
FROM php:8.3-apache

# set default apache working directory
WORKDIR /var/www

# copy project files to container
# COPY . /var/www/

# update packages and install necessary dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    rpm
    
# install necessary PHP extensions
RUN docker-php-ext-install pdo_mysql intl

# install OPCache
RUN docker-php-ext-install opcache
