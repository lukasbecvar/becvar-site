#!/bin/bash

clear

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# PHPSTAN analyze config
yellow_echo 'PHPSTAN: testing...'
php vendor/bin/phpstan analyze

# PHPUnit run tests
yellow_echo 'PHPUnit: testing...'
php bin/phpunit
