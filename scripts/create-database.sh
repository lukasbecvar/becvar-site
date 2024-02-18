#!/bin/bash

# create database
php bin/console doctrine:database:create
php bin/console doctrine:database:create --env=test
