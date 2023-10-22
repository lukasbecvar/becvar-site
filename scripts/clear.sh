#!/bin/bash

# clean app & cache
php bin/console cache:clear

# delete composer files
rm -rf composer.lock
rm -rf vendor/

# delete npm packages
rm -rf node_modules/
rm -rf package-lock.json

# delete symfony cache folder
rm -rf var/
