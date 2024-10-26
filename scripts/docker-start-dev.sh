#!/bin/bash

# install & build assets
sh scripts/install.sh

# build docker containers
docker-compose up --build
