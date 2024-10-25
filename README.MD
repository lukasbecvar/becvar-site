## About becvar-site
Personal website build with the Symfony framework & Bootstrap.

## Public site
![Homepage](.github/images/public-page.png)
- Main components: about, projects list, contact, etc...

## Admin site
![Dashboard](.github/images/admin-dashboard.png)
- Account system with permissions
- Warning system
- Dashboard
- Database browser
- Inbox for contact form
- Advanced log system

## Installation (with mysql/apache)
- install app [Requirements](#requirements)
- clone repository to your /www dir
- configure apache config like [apache-site.conf](./docker/configs/apache-site.conf)
- for production use only APP_ENV=prod! (in .env)
- configure app enviroment in .env.prod
- install dependencies `composer install && npm install && npm run build`
- create database `doctrine:database:create`
- migrate database structure `doctrine:migrations:migrate --no-interaction`
- install opcache (optionally)

## Development environment
The complete stack for development is in Docker Compose, including PHP, Node, and Composer. You just need to run the docker-start script to launch it.

## Deployment
Deployment of the new version with GitHub Action when a new release is created.

## Requirements
- Apache / Nginx PHP compatible web server
- PHP 8.3
- PHP-PDO_MYSQL
- PHP-INTL
- PHP-GD

## Dependencies
* Symfony
   * [Website](https://symfony.com/)   
* Bootstrap
   * [Website](https://getbootstrap.com/)
* Boxicons
   * [Website](https://boxicons.com/)
* Fontawesome
   * [Website](https://fontawesome.com)
* Purecounterjs
   * [Github](https://github.com/srexi/purecounterjs)
* Chartjs
   * [Github](https://github.com/chartjs/Chart.js)
* PhpunitPrettyPrint
   * [Github](https://github.com/indentno/phpunit-pretty-print)
   
## License
This software is licensed under the [MIT license](https://github.com/lukasbecvar/becvar-site/blob/main/LICENSE)
