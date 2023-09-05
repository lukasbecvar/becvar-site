#!/bin/bash

# clear console in script start
clear

# print panel menu
echo "\033[33m\033[1m╔═══════════════════════════════════════════╗\033[0m"
echo "\033[33m\033[1m║\033[1m                 \033[32mWEB PANEL\033[0m                 \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╠═══════════════════════════════════════════╣\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m1: Start dev server\033[1m   \033[34m2: Build production\033[0m \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m3: Run tests\033[0m                              \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[0m                                           \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m4: Run installer\033[0m                          \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╠═══════════════════════════════════════════╣\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m0: Exit panel\033[0m                             \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╚═══════════════════════════════════════════╝\033[0m"

# stuck menu for select action
read number

# clear console with select
clear

# select action
case $number in

	1) # run developer server
		sh scripts/start.sh
	;;
	2) # run build structure
		sh scripts/build_prod.sh
	;;
	3) # run tests
		./vendor/bin/phpunit tests
	;;
	4) # run install components
		sh scripts/install.sh
	;;
	0) # exit this panel
		exit
	;;
	*) # error msg
		echo "\033[31m\033[1m$number: not found!\033[0m"
	;;
esac