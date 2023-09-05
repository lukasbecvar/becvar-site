<?php // main site manager (get basic values)

    namespace becwork\managers;

	class SiteManager {

        // get true or false if admin page
        public function isCurrentPageAdmin() {
            
            // default state output
			$state = false;

            // check if page is admin
            if ($this->getQueryString("admin") != null) {
                $state = true;
            }

            return $state;
        }

        // check maintenance mode
        public function ifMaintenance() {

            global $config;

            // default state output
			$state = false;

            // check if maintenance mode valid
            if ($config->getValue('maintenance') == "enabled" && $this->isCurrentPageAdmin() == false) {
                $state = true;
            }

            return $state;
        }

        // get query string by name
        public function getQueryString($query) {
            
            global $escapeUtils;

            // default query string
            $query_string = null;

            // check if query is empty
            if (!empty($_GET[$query])) {

                // get & escape paramater
                $query_string = $escapeUtils->specialCharshStrip($_GET[$query]);
            }

            // return final query value
            return $query_string;
        }

        // get Http host aka domain name
        public function getHTTPhost() {
            $http_host = $_SERVER['HTTP_HOST'];
            return $http_host;
        }

        // get page title by paramater
        public function getPageTitle() {

            global $config;

            // check if host is localhost
            if ($this->getHTTPhost() == "localhost") {
                
                $title = "Localhost Testing"; 
            } else {

                // check if admin site
                if ($this->isCurrentPageAdmin()) { 
                
                    $title =  "Admin site"; 
                } else {

                    // set main app name
                    $title = $config->getValue('appName'); 
                }
            }

            return $title;
        }

        // get age by birth data input
        public function getAge($birth_date) {

            // explode string data to array
            $birth_date = explode("/", $birth_date);

            // calculate age
            $age = (date("md", date("U", mktime(0, 0, 0, $birth_date[0], $birth_date[1], $birth_date[2]))) > date("md") ? ((date("Y") - $birth_date[2]) - 1) : (date("Y") - $birth_date[2]));
            return $age;           
        }

        // handle error msg & code
        public function handleError($msg, $code) {

            // send response code
            http_response_code($code);

            // check if site enabled dev-mode
            if ($this->isSiteDevMode()) {
                die("[DEV-MODE]: ".$msg);
            } else {
                $this->redirectError($code);
            }
        }

        // redirect to error page
        public function redirectError($error) {

            // redirct loaction header
            header("location: error.php?code=$error");
        }

        // check if page in dev mode
        public function isSiteDevMode() {

            global $config;

            // default state output
			$state = false;

            // check if dev mode enabled
            if ($config->getValue("dev-mode") == true) {
                $state = true;
            }
            
            return $state;
        }

        // check if site running on localhost
        public function isRunningLocalhost() {

            global $config;

            // default state output
			$state = false;

            // check if running on url localhost
            if ($config->getValue("url") == "localhost") {
                $state = true;
            } 
            
            // check if running on localhost ip
            if ($config->getValue("url") == "127.0.0.1") {
                $state = true;
            }

            return $state;
        }
	}
?>