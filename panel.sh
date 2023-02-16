#!/bin/bash

# clear console in script start
clear

# print panel menu
echo "\033[33m\033[1m##########################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                       \033[32mWEB PANEL\033[0m                      \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##########################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                                                      \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m1: Start dev server\033[0m        \033[33m2: Build production\033[0m     \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m3: Run tests\033[0m                                       \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                      \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m5: Run installer\033[0m                                   \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                      \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##########################################################\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m0: Exit panel\033[0m                                      \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##########################################################\033[0m"

# stuck menu for select action
read selector

# clear console with select
clear

# selector methodes
case $selector in

	1*) # run developer server
		sh scripts/start.sh
	;;
	2*) # run build structure
		sh scripts/build_prod.sh
	;;
	3*) # run tests
		php tests/ResponseTest.php
		php tests/CryptTest.php
		php tests/HashTest.php
	;;
	4*) # run image dumper
		php scripts/ImageDumper.php
	;;	
	5*) # run install components
		sh scripts/install.sh
	;;
	0*) # exit this panel
		exit
	;;
	*) # error msg
		echo "\033[31m\033[1m$selector: not found!\033[0m \n"
	;;
esac