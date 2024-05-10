#!/bin/bash

# check if script is running from GitHub Actions
if [ "$1" != "pkn3tdo9kdn" ]; then
    echo "This script can only be executed from GitHub Actions."
    exit 1
fi

# pre deploy actions
cd /services/website/becvar.xyz
sudo systemctl stop apache2
sudo sh scripts/clear.sh
          
# pull new version
git pull

# config env
sed -i 's/^\(APP_ENV=\)dev/\1prod/' .env

# install packages
sh scripts/install.sh

# migrate database
rm -rf migrations
mkdir migrations
php bin/console doctrine:database:create --if-not-exists
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
          
# execute app commands
php bin/console projects:list:update
php bin/console auth:tokens:regenerate
          
# fix storage permissions
sudo chmod -R 777 var/

# start apache
sudo systemctl start apache2
