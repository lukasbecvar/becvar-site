#!/bin/bash

# The website builder for create production file structure -> build/

clear # clear console after start script

# color codes.
green_echo (){ echo "$(tput setaf 2)$1"; }
yellow_echo () { echo "$(tput setaf 3)$1"; }
red_echo () { echo "$(tput setaf 9)$1"; }
cecho () { echo "$(tput setaf 6)$1"; }


# delete old build if exist
if [ -d "build/" ] 
then
	sudo rm -r build/
fi

green_echo "Building website..."

# build website
mkdir build/
cp -R app/ build/app/
cp -R public/ build/public/
cp -R scripts/ build/scripts/
cp -R site/ build/site/
cp -R tests/ build/tests/
cp services-list.php build/
cp browser-list.php build/
cp composer.json build/
cp composer.phar build/
cp config.php build/
cp panel.sh build/

# print status msg
green_echo "Website builded in build folder"
green_echo "Warning: Check config before upload on server!"