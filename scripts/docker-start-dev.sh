#!/bin/bash

# install & build assets
sh scripts/install.sh

# stop previous containers
docker-compose down

# build docker containers
docker-compose up --build
