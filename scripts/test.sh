#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console
clear

# load testing data fixtures
php bin/console doctrine:fixtures:load --no-interaction --env=test

# PHPcs check # fix
yellow_echo 'PHPCS: testing...'
php vendor/bin/phpcbf
php vendor/bin/phpcs

# PHPstan analyze
yellow_echo 'PHPSTAN: testing...'
php vendor/bin/phpstan analyze

# PHPUnit tests
yellow_echo 'PHPUnit: testing...'
php bin/phpunit
