#!/bin/bash

# clear console in script start
clear

# print panel menu
echo "\033[33m\033[1m╔═══════════════════════════════════════════╗\033[0m"
echo "\033[33m\033[1m║\033[1m                 \033[32mWEB PANEL\033[0m                 \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╠═══════════════════════════════════════════╣\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m1: Start dev server\033[1m   \033[34m2: Build\033[0m            \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m3: Run tests\033[1m          \033[34m4: Clear\033[0m            \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m5: Run migration\033[1m                          \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[0m                                           \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m6: Run installer\033[0m                          \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╠═══════════════════════════════════════════╣\033[0m"
echo "\033[33m\033[1m║\033[1m \033[34m0: Exit panel\033[0m                             \033[33m\033[1m║\033[0m"
echo "\033[33m\033[1m╚═══════════════════════════════════════════╝\033[0m"

# stuck menu for select action
read number

# select action
case $number in

	1) # run developer server
		sh scripts/start.sh
	;;
	2) # run build structure
		sh scripts/build.sh
	;;
	3) # run tests
		sh scripts/test.sh
	;;
    4) # run clear
        sh scripts/clear.sh
    ;;
    5)
        sh scripts/migrate.sh
    ;;
	6) # run install components
		sh scripts/install.sh
	;;
	0) # exit this panel
		exit
	;;
	*) # error msg
		echo "\033[31m\033[1m$number: not found!\033[0m"
	;;
esac
