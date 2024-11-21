#!/bin/bash

# delete composer files
sudo rm -rf vendor/
sudo rm -rf composer.lock

# delete npm packages
sudo rm -rf node_modules/
sudo rm -rf package-lock.json

# delete builded assets
sudo rm -rf public/build/
sudo rm -rf public/bundles/

# delete symfony cache folder
sudo rm -rf var/

# delete docker services data
sudo rm -rf .docker/services/
