#!/bin/bash

#Clear console in script start
clear

#Print panel menu
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                                \033[32mWEB PANEL\033[0m                               \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m1    -   Start dev server\033[0m        \033[33m2   -   Build production\033[0m            \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m3    -   Run tests\033[0m               \033[33m4   -   Dump images\033[0m                 \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m5    -   Run installer\033[0m                                               \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m0    -   Exit panel\033[0m                                                  \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"

#Stuck menu for select action
read selector

#Clear console with select
clear

#Selector methodes
case $selector in

	1*) #Run developer server
		sh scripts/start.sh
	;;
	2*) #Run build structure
		sh scripts/build_prod.sh
	;;
	3*) #Run tests
		php tests/ResponseTest.php
		php tests/CryptTest.php
		php tests/HashTest.php
	;;
	4*) #Run image dumper
		php scripts/ImageDumper.php
	;;	
	5*) #Run install components
		sh scripts/install.sh
	;;
	0*) #Exit this panel
		exit
	;;
	*) #Error msg
		echo "\033[33mYour vote not found!\033[0m"
	;;
esac