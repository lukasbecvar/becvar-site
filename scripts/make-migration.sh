#!/bin/bash

# create new migration version
docker-compose run php php bin/console make:migration --no-interaction
