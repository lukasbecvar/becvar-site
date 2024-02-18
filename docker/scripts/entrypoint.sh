#!/bin/bash

# wait to database server start
echo "waiting for database migration 30s..."
sleep 30

# create database
./scripts/create-database.sh

# migrate database
./scripts/migrate.sh

# start apache
apache2-foreground
