#!/bin/bash

# color codes.
red_echo () { echo "$(tput setaf 9)$1"; }
die() { echo "$*" 1>&2 ; exit 1; }

# start server script for development use -> php, database server

# check if vendor installed
if [ ! -d "vendor/" ]
then
    red_echo "Build-error: vendor not found, please install composer"
    die
fi

# clear console
clear

# go to public
cd public/ 

# start mysql
sudo systemctl start mysql

# print mysql status
sudo systemctl --no-pager status mysql

# run php dev server
sudo php -S localhost:80