## About becvar-site
Personal website builded with symfony framework, bootstrap & webpack-encore

## Public site
 - Main components: about, projects list, contact, etc...
 - Projects component gets repos from github (closed, open / Archived, Unarchived)
 - Image uploader / view
 - Code paste site

## Admin site
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

## TODOS
- [ ] Terminal in admin
- [ ] File manager & config editor
- [ ] add more translations
- [ ] Complte refactor & code format
- [ ] PHPUnit tests

## Dependencies 
* Symfony
   * Version: **6.3**
   * [Website](https://symfony.com/)   
* Mobile-Detect
   * Version: **3.74.0**
   * [Github](https://github.com/serbanghita/Mobile-Detect)
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
* Waypoints
   * Version: **4.0.1**
   * [Github](https://github.com/imakewebthings/waypoints)
* Purecounterjs
   * [Github](https://github.com/srexi/purecounterjs)
* Light-Gallery
   * Version: **1.10.0**
   * [Github](https://github.com/sachinchoolur/lightGallery)

## License
The framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
