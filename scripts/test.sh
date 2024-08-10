#!/bin/bash

# define colors
yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console output
clear

# load testing data fixtures
php bin/console doctrine:fixtures:load --no-interaction --env=test

# test & fix php codesniffer
yellow_echo 'PHPCS: testing...'
php vendor/bin/phpcbf
php vendor/bin/phpcs

# analyse with phpstan
yellow_echo 'PHPSTAN: testing...'
php vendor/bin/phpstan analyze

# test with phpunit
yellow_echo 'PHPUnit: testing...'
php bin/phpunit
