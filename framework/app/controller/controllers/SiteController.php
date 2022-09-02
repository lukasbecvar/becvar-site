<?php //The Example app controller

	class SiteController {

        //Get true or false if admin page
        public function isCurrentPageAdmin() {
            if (!empty($_GET["admin"])) {
                return true;
            } else {
                return false;
            }
        }

        //Get true or false for maintenance mode
        public function ifMaintenance() {

            global $pageConfig;
            global $mysqlUtils;

            if (($pageConfig->getValueByName('maintenance') == "enabled" && $this->isCurrentPageAdmin() == false) or $mysqlUtils->isOffline()) {
                return true;
            }
        }

        //Get process name if isset
        public function getCurrentProcess() {

            global $mysqlUtils;

            if (isset($_GET["process"])) {
               return $mysqlUtils->escapeString($_GET["process"], true, true);
            } else {
                if ($this->isCurrentPageAdmin()) {
                    return "dashboard";
                } else {
                    return null;
                }
            }
        }

        //Get admin process name if isset
        public function getCurrentAdminProcess() {

            global $mysqlUtils;

            if (isset($_GET["admin"])) {
               return $mysqlUtils->escapeString($_GET["admin"], true, true);
            } else {
                return null;
            }
        }

        //Get Http host aka domain name
        public function getHTTPhost() {
            return $_SERVER['HTTP_HOST'];
        }

        //Get page title by paramater
        public function getPageTitle() {

            global $pageConfig;

            if ($this->getHTTPhost() == "localhost") {
                return "Localhost Testing"; 
            } else {
                if ($this->isCurrentPageAdmin()) { 
                    return "Admin site"; 
                } else {
                    return $pageConfig->getValueByName('appName'); 
                }
            }
        }

        //Get action name if isset
        public function getCurrentAction() {

            global $mysqlUtils;

            if (isset($_GET["action"])) {
               return $mysqlUtils->escapeString($_GET["action"], true, true);
            }
        } 

        //Get method name if isset
        public function getCurrentMethod() {

            global $mysqlUtils;

            if (isset($_GET["method"])) {
                $method = $mysqlUtils->escapeString($_GET["method"], true, true);
                return $method;
            }
        }

        //Get age by birth data input
        public function getAge($birthDate) {
            $birthDate = explode("/", $birthDate);
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") 
            ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
            return $age;           
        }
	}
?>