#!/bin/bash

clear

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

yellow_echo 'testing: ./config'
php vendor/bin/phpstan analyze --level 0 ./config

yellow_echo 'testing: ./public/index.php'
php vendor/bin/phpstan analyze --level 0 ./public/index.php

yellow_echo 'testing: ./src'
php vendor/bin/phpstan analyze --level 0 ./src

yellow_echo 'testing: ./tests'
php vendor/bin/phpstan analyze --level 0 ./tests
