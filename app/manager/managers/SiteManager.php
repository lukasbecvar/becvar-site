<?php // main site manager (get basic values)

    namespace becwork\managers;

	class SiteManager {

        // get true or false if admin page
        public function is_admin_site(): bool {
            
            // default state output
			$state = false;

            // check if page is admin
            if ($this->get_query_string("admin") != null) {
                $state = true;
            }

            return $state;
        }

        // check maintenance mode
        public function is_maintenance(): bool {

            global $config;

            // default state output
			$state = false;

            // check if maintenance mode valid
            if ($config->get_value('maintenance') == "enabled" && $this->is_admin_site() == false) {
                $state = true;
            }

            return $state;
        }

        // get query string by name
        public function get_query_string($query): ?string {
            
            global $escape_utils;

            // default query string
            $query_string = null;

            // check if query is empty
            if (!empty($_GET[$query])) {

                // get & escape paramater
                $query_string = $escape_utils->special_chars_strip($_GET[$query]);
            }

            // return final query value
            return $query_string;
        }

        // get Http host aka domain name
        public function get_http_host() {
            $http_host = $_SERVER['HTTP_HOST'];
            return $http_host;
        }

        // get page title by paramater
        public function get_page_title() {

            global $config;

            // check if host is localhost
            if ($this->is_running_localhost()) {
                
                $title = "Localhost Testing"; 
            } else {

                // check if admin site
                if ($this->is_admin_site()) { 
                
                    $title =  "Admin site"; 
                } else {

                    // set main app name
                    $title = $config->get_value('app-name'); 
                }
            }

            return $title;
        }

        // get age by birth data input
        public function get_age($birth_date) {

            // explode string data to array
            $birth_date = explode("/", $birth_date);

            // calculate age
            $age = (date("md", date("U", mktime(0, 0, 0, $birth_date[0], $birth_date[1], $birth_date[2]))) > date("md") ? ((date("Y") - $birth_date[2]) - 1) : (date("Y") - $birth_date[2]));
            return $age;           
        }

        // redirect to error page
        public function redirect_error($error) {

            global $url_utils;

            // redirct loaction header
            $url_utils->js_redirect("error.php?code=$error");
        }

        // handle error msg & code
        public function handle_error($msg, $code): void {

            // check if site enabled dev-mode
            if ($this->is_dev_mode()) {

                // send response code
                http_response_code($code);

                // print error msg & die app
                die("[DEV-MODE]: ".$msg);
            } else {
                $this->redirect_error($code);
            }
        }

        // check if page in dev mode
        public function is_dev_mode() {

            global $config;

            // default state output
			$state = false;

            // check if dev mode enabled
            if ($config->get_value("dev-mode") == true) {
                $state = true;
            }
            
            return $state;
        }

        // check if site running on localhost
        public function is_running_localhost() {

            // default state output
			$state = false;

            // get http host
            $host = $this->get_http_host();
            
            // check if running on url localhost
            if (str_starts_with($host, "localhost")) {
                $state = true;
            } 
            
            // check if running on localhost ip
            if (str_starts_with($host, "127.0.0.1")) {
                $state = true;
            }

            return $state;
        }
	}
