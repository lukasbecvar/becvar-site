#!/bin/bash

# clean app & cache
php bin/console cache:clear

# delete composer files
rm -rf composer.lock
rm -rf vendor/

# delete npm packages
rm -rf node_modules/
rm -rf package-lock.json

# delete builded assets
rm -rf public/build/

# delete symfony cache folder
sudo rm -rf var/

# delete docker services data
sudo rm -rf _docker/services/
