FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    rpm \ 
    libzip-dev \
    unzip \ 
    && docker-php-ext-install zip pdo_mysql mysqli
