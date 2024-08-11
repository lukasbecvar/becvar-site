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
 - clone repository to your /www dir
 - configure apache config like [apache-site.conf](./docker/configs/apache-site.conf)
 - for production use only APP_ENV=prod! (in .env)
 - configure app enviroment in .env.prod
 - configure all values (mysql app secret etc)
 - install dependencies (scripts/install.sh)
 - migrate database structure (scripts/migrate.sh)
 - install opcache & acpu (optionally)

## Deployment
Deployment of the new version with GitHub Action when a new release is created.

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
* PhpunitPrettyPrint
   * [Github](https://github.com/indentno/phpunit-pretty-print)
   
## License
The framework is open-sourced software licensed under the [MIT license](https://github.com/lordbecvold/becvar-site/blob/main/LICENSE)
