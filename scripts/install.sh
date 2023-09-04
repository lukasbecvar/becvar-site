#!/bin/bash

# installation script for development & production use
# install: all requirements

# color codes.
red_echo () { echo "$(tput setaf 9)$1"; }

# install composer
php composer.phar upgrade
php composer.phar update
