#!/bin/bash

# install all application requirements
composer install
npm install
sh scripts/build.sh
