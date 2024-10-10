#!/bin/bash

# drop databases
docker-compose run php bash -c "
    php bin/console doctrine:database:drop --force &&
    php bin/console doctrine:database:drop --env=test --force
"
