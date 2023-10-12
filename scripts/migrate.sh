#!/bin/bash

# database migration for update database structure
php bin/console make:migration
php bin/console doctrine:migrations:migrate
