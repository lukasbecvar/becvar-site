## Overview
# This Dockerfile sets up a PHP 8.1 environment with Apache web server.
# it installs necessary dependencies, configures Apache, and copies application files.
FROM php:8.1-apache

# set working directory inside the container
WORKDIR /var/www

# update package lists and install dependencies
RUN apt-get update && apt-get install -y \
    rpm \ 
    libzip-dev \
    unzip \ 
    && docker-php-ext-install zip pdo_mysql

# copy Apache site configuration
COPY ./docker/configs/apache-site.conf /etc/apache2/sites-available/000-default.conf

# enable Apache rewrite module
RUN a2enmod rewrite

# copy application files from host to container
COPY . .

# make shell scripts executable
RUN chmod +x scripts/create-database.sh
RUN chmod +x scripts/migrate.sh

# copy entrypoint script to container
COPY ./docker/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh

# make entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# set default command to run entrypoint script
CMD ["entrypoint.sh"]
