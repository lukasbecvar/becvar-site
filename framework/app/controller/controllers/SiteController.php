<?php // main site controller

    namespace becwork\controllers;

	class SiteController {

        // get true or false if admin page
        public function isCurrentPageAdmin() {
            
            // check if page is admin
            if (!empty($_GET["admin"])) {
                return true;
            } else {
                return false;
            }
        }

        // check maintenance mode
        public function ifMaintenance() {

            global $pageConfig;
            global $mysqlUtils;

            // check if maintenance mode valid
            if (($pageConfig->getValueByName('maintenance') == "enabled" && $this->isCurrentPageAdmin() == false) or $mysqlUtils->isOffline()) {
                return true;
            }
        }

        // get process name if isset
        public function getCurrentProcess() {

            global $mysqlUtils;

            if (isset($_GET["process"])) {

                // return escaped process
                return $mysqlUtils->escapeString($_GET["process"], true, true);
            } else {

                // check if page is dashboard
                if ($this->isCurrentPageAdmin()) {
                    return "dashboard";
                } else {
                    return null;
                }
            }
        }

        // get admin process name if isset
        public function getCurrentAdminProcess() {

            global $mysqlUtils;

            if (isset($_GET["admin"])) {

                // return escaped admin get
                return $mysqlUtils->escapeString($_GET["admin"], true, true);
            } else {
                return null;
            }
        }

        // get Http host aka domain name
        public function getHTTPhost() {
            return $_SERVER['HTTP_HOST'];
        }

        // get page title by paramater
        public function getPageTitle() {

            global $pageConfig;

            // check if host is localhost
            if ($this->getHTTPhost() == "localhost") {
                return "Localhost Testing"; 
            } else {

                // check if admin site
                if ($this->isCurrentPageAdmin()) { 
                    return "Admin site"; 
                } else {

                    // return app name
                    return $pageConfig->getValueByName('appName'); 
                }
            }
        }

        // get action name if isset
        public function getCurrentAction() {

            global $mysqlUtils;

            if (isset($_GET["action"])) {

                // return escaped action
                return $mysqlUtils->escapeString($_GET["action"], true, true);
            }
        } 

        // get method name if isset
        public function getCurrentMethod() {

            global $mysqlUtils;

            if (isset($_GET["method"])) {

                // retun escaped method
                return $mysqlUtils->escapeString($_GET["method"], true, true);
            }
        }

        // get age by birth data input
        public function getAge($birthDate) {

            // explode string data to array
            $birthDate = explode("/", $birthDate);

            // calculate age
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
            return $age;           
        }

        // check if page in dev mode
        public function isSiteDevMode() {

            global $pageConfig;

            // check if dev mode enabled
            if ($pageConfig->getValueByName("dev_mode") == true) {
                return true;
            } else {
                return false;
            }
        }
	}
?>