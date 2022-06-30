#!/bin/bash

# The website builder for create production file structure -> build/

clear #Clear console after start script

#Color codes.
green_echo (){ echo "$(tput setaf 2)$1"; }
yellow_echo () { echo "$(tput setaf 3)$1"; }
red_echo () { echo "$(tput setaf 9)$1"; }
cecho () { echo "$(tput setaf 6)$1"; }


#Delete old build if exist
if [ -d "build/" ] 
then
	sudo rm -r build/
fi

green_echo "Building website..."

#Build website
mkdir build/
cp -R framework/ build/framework/
cp -R public/ build/public/
cp -R scripts/ build/scripts/
cp -R site/ build/site/
cp -R tests/ build/tests/
cp composer.json build/
cp composer.phar build/
cp config.php build/
cp panel.sh build/

#Print status msg
green_echo "Website builded in build folder"
green_echo "Warning: Check config before upload on server!"