#!/bin/bash

# install all application requirements

# install composer
if [ ! -d 'vendor/' ]
then
    composer install
fi

# install node modules
if [ ! -d 'node_modules/' ]
then
    npm install
fi

# build assets
if [ ! -d 'public/build/' ]
then
    sh scripts/build.sh
fi

# fix storage permissions
sudo chmod -R 777 var/
