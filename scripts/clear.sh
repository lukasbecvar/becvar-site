#!/bin/bash

# clean app & cache
php bin/console cache:clear
rm -rf composer.lock
rm -rf vendor/
rm -rf var/
