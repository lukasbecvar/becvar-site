## About becvar-site
My personal website app build with symfony framework

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
 - Set apache root dir to public/
 - Install dependencies (scripts/install.sh)
 - Migrate database structure (scripts/migrate.sh)
 - Set service path in scripts (if not same /services/)
 - Set custom error pages (ErrorDocument 400 /error/400, etc)
 - Configure all values (mysql app secret etc)
 - For production use only APP_ENV=prod! (in .env)
 - Configure app enviroment in .env.prod
 - Disable apache autoindex module

## Required
 - php-8.1 or <
 - mysql8 or <
 - php-curl
 - php-pdo

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
* Jquery
   * Version: **3.6.0**
   * [Website](https://jquery.com/)
* Bootstrap
   * Version: **5.1.3**
   * [Website](https://getbootstrap.com/)
* Boxicons
   * Version: **2.1.2**
   * [Website](https://boxicons.com/)
* Font-awesome
   * Version: **5.8.1 - FREE**
   * [Website](https://fontawesome.com)
* Remixicon
   * Version: **2.5.0**
   * [Website](https://remixicon.com/)
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
