<?php //Controll action change get config in database table config 

    namespace becwork\controllers;

    class PageConfigController {

        //Update maintenance value in config to enable
        public function maintenanceEnable() {

            global $mysqlUtils;
            global $adminController;

            //Edit value in config file
            file_put_contents("../config.php", str_replace("disabled", "enabled", file_get_contents("../config.php")));
        
            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        //Update maintenance value in config to disable
        public function maintenanceDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            //Edit value in config file
            file_put_contents("../config.php", str_replace("enabled", "disabled", file_get_contents("../config.php")));

            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }

        //Update dev mode value in config to enable
        public function devModeEnable() {

            global $mysqlUtils;
            global $adminController;

            //Edit value in config file
            file_put_contents("../config.php", str_replace('"dev_mode"    => false', '"dev_mode"    => true', file_get_contents("../config.php")));
        
            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        //Update dev mode value in config to disable
        public function devModeDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            //Edit value in config file
            file_put_contents("../config.php", str_replace('"dev_mode"    => true', '"dev_mode"    => false', file_get_contents("../config.php")));

            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }

        //Update api enabled value in config to enable
        public function apiEnable() {

            global $mysqlUtils;
            global $adminController;

            //Edit value in config file
            file_put_contents("../config.php", str_replace('"apiEnable" => false', '"apiEnable" => true', file_get_contents("../config.php")));
        
            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        //Update api enabled value in config to disable
        public function apiDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            //Edit value in config file
            file_put_contents("../config.php", str_replace('"apiEnable" => true', '"apiEnable" => false', file_get_contents("../config.php")));

            //Log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }
    }
?>