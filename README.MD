## About becvar-site
Personal website builded with symfony framework, bootstrap & webpack-encore

## Admin site
the main part of this application is the admin panel which contains functions and components for administration of the host linux server
 - Account system with permissions
 - Warning system
 - Server dashboard
 - Services manager
 - Custom database viewer
 - Private todo manager
 - Media browser (Image uploader)
 - Inbox for contact form
 - Advanced log system
 - Server Emergency shutdown
 - Included adminer in admin page
 - Chat system

## Public site
the public part of the site contains information about me and my projects
 - Main components: about, projects list, contact, etc...
 - Projects component gets repos from github (closed, open / Archived, Unarchived)
 - Image uploader / view
 - Code paste site

## Installation (with apache)
 - install apache2, mysql8, composer, php, php-pdo, php-mysqli, php-xml, php-dom
 - clone repository to your /www dir
 - disable apache autoindex module
 - configure apache config like [apache-site.conf](./apache-site.conf)
 - for production use only APP_ENV=prod! (in .env)
 - configure app enviroment in .env.prod
 - configure all values (mysql app secret etc)
 - install dependencies (scripts/install.sh)
 - migrate database structure (scripts/migrate.sh)

## Translations
 - Public page is automatically translated into visitor language (geolocation with ip address)

## Assets builder
 - Webpack (symfony-encore), scripts/build.sh

## Required
 - php-8.1 or <
 - mysql8 or <
 - php-curl
 - php-pdo
 - nodejs 18 or <
 - npm 9 or <

## Dependencies 
* Symfony
   * Version: **6.3**
   * [Website](https://symfony.com/)   
* Adminer
   * Version: **4.8.1**
   * [Website](https://www.adminer.org/)
* Geo-Plugin
   * [Website](http://www.geoplugin.net/)
* Bootstrap
   * Version: **4.0.0**
   * [Website](https://getbootstrap.com/)
* Boxicons
   * Version: **2.1.2**
   * [Website](https://boxicons.com/)
* Font-awesome
   * Version: **5.8.1 - FREE**
   * [Website](https://fontawesome.com)
* Purecounterjs
   * [Github](https://github.com/srexi/purecounterjs)
* Light-Gallery
   * Version: **1.10.0**
   * [Github](https://github.com/sachinchoolur/lightGallery)

## License
The framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
