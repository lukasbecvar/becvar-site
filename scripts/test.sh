#!/bin/bash

clear

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

yellow_echo 'PHPSTAN: testing: ./config'
php vendor/bin/phpstan analyze --level 5 ./config

yellow_echo 'PHPSTAN: testing: ./public/index.php'
php vendor/bin/phpstan analyze --level 5 ./public/index.php

yellow_echo 'PHPSTAN: testing: ./src'
php vendor/bin/phpstan analyze --level 5 ./src

yellow_echo 'PHPSTAN: testing: ./tests'
php vendor/bin/phpstan analyze --level 5 ./tests

yellow_echo 'PHPUNIT: testing...'
php vendor/bin/phpunit