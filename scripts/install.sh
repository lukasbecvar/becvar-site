#!/bin/bash

# install composer packages
if [ ! -d 'vendor/' ]
then
    docker-compose run composer install --ignore-platform-reqs
fi

# install node-modules packages
if [ ! -d 'node_modules/' ]
then
    docker-compose run node npm install --loglevel=error
fi

# build assets
if [ ! -d 'public/build/' ]
then
    docker-compose run node npm run build
fi

# fix storage permissions
sudo chmod -R 777 var/
sudo chown -R www-data:www-data var/
