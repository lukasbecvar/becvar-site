#!/bin/bash

# function to add color and label to each line
add_color_label() {
    local color="$1"
    local label="$2"
    while IFS= read -r line; do
        echo "\033[0;${color}m${label}: ${line}\033[0m"
    done
}

# install & build assets
sh scripts/install.sh

# start npm watch in background, preserve color output
npm run watch --silent 2>&1 | add_color_label "32" "npm" &

# build docker containers, preserve color output
sudo docker-compose up --build 2>&1 | add_color_label "36" "docker"
