<?php //The main site contorller and getter

    class SiteController {

        //Get true or false if admin page
        public function isCurrentPageAdmin() {
            if (!empty($_GET["page"]) && $_GET["page"] == "admin") {
                return true;
            } else {
                return false;
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



        //Get action name if isset
        public function getCurrentAction() {

            global $mysqlUtils;

            if (isset($_GET["action"])) {
               return $mysqlUtils->escapeString($_GET["action"], true, true);
            }
        }


        //Get Http host aka domain name
        public function getHTTPhost() {
            return $_SERVER['HTTP_HOST'];
        }


        //Check if process isset and return true or false
        public function isProcessEmpty() {
            if (empty($_GET["process"])) {
                return true;
            } else {
                return false;
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


        //Get site protocol
        public function getSiteProtocol() {
            return stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';;
        }


        //Get true or false for maintenance mode
        public function ifMaintenance() {

            global $pageConfig;
            global $mysqlUtils;

            if (($pageConfig->getValueByName('maintenance') == "enabled" && $this->getCurrentPage() != "admin") or $mysqlUtils->isOffline()) {
                return true;
            }
        }


        //Get current page value if isset
        public function getCurrentPage() {

            global $mysqlUtils;

            if (isset($_GET["page"])) {
                return $mysqlUtils->escapeString($_GET["page"], true, true);
            } else {
                return "home";
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
