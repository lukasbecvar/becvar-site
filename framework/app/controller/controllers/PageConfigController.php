<?php // config controller 

    namespace becwork\controllers;

    class PageConfigController {

        // update maintenance value in config to enable
        public function maintenanceEnable() {

            global $mysqlUtils;
            global $adminController;

            // edit value in config file
            file_put_contents("../config.php", str_replace("disabled", "enabled", file_get_contents("../config.php")));
        
            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        // update maintenance value in config to disable
        public function maintenanceDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            // edit value in config file
            file_put_contents("../config.php", str_replace("enabled", "disabled", file_get_contents("../config.php")));

            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }

        // update dev mode value in config to enable
        public function devModeEnable() {

            global $mysqlUtils;
            global $adminController;

            // edit value in config file
            file_put_contents("../config.php", str_replace('"dev-mode"    => false', '"dev-mode"    => true', file_get_contents("../config.php")));
        
            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        // update dev mode value in config to disable
        public function devModeDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            // edit value in config file
            file_put_contents("../config.php", str_replace('"dev-mode"    => true', '"dev-mode"    => false', file_get_contents("../config.php")));

            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }

        // update api enabled value in config to enable
        public function apiEnable() {

            global $mysqlUtils;
            global $adminController;

            // edit value in config file
            file_put_contents("../config.php", str_replace('"apiEnable" => false', '"apiEnable" => true', file_get_contents("../config.php")));
        
            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Activated maintenance mode");
        }

        // update api enabled value in config to disable
        public function apiDisable() {

            global $mysqlUtils;
            global $adminController;
                        
            // edit value in config file
            file_put_contents("../config.php", str_replace('"apiEnable" => true', '"apiEnable" => false', file_get_contents("../config.php")));

            // log to mysql
            $mysqlUtils->logToMysql("Config update", $adminController->getCurrentUsername()." Deactivated maintenance mode");
        }
    }
?>