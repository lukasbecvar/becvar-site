#!/bin/bash

# drop database and migrate for create tables
sh scripts/drop-database.sh
sh scripts/migrate.sh

# load testing datafixtures
php bin/console doctrine:fixtures:load --no-interaction 
php bin/console doctrine:fixtures:load --no-interaction --env=test
