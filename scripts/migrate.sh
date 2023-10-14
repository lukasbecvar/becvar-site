#!/bin/bash

# create database
php bin/console doctrine:database:create

# database migration for update database structure
php bin/console make:migration
php bin/console doctrine:migrations:migrate
