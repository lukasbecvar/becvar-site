#!/bin/bash

# start server script for development use -> php, database server

# color codes.
red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

# print warning
red_echo 'Warning: use this server only for local development'

# check if vendor installed
if [ ! -d 'vendor/' ]
then
    red_echo 'Build-error: vendor not found, please install composer'
    exit
fi

# clear console
clear

# go to public
cd public/ 

# start mysql
sudo systemctl start mysql

# print mysql status
systemctl --no-pager status mysql

# start npm watch in background
npm run watch &

# start Symfony server in background
symfony server:start 
