#!/bin/bash

# stop web server
sudo systemctl stop apache2

# clear requirements & cache
sudo sh scripts/clear.sh

# pull the latest changes
git pull

# set the environment to production
sed -i 's/^\(APP_ENV=\)dev/\1prod/' .env

# install dependencies
sh scripts/install.sh

# migrate database to latest version
docker-compose run php bin/console doctrine:database:create --if-not-exists
docker-compose run php php bin/console doctrine:migrations:migrate --no-interaction
                    
# run app commands
docker-compose run php php bin/console projects:list:update

# fix storage permissions
sudo chmod -R 777 var/
sudo chown -R www-data:www-data var/

# start apache
sudo systemctl start apache2
