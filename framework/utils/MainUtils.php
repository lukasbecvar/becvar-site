<?php //Main utils class

    class MainUtils { 


        /*
          * The function for get php server infromation
          * Usage like echo drawPhpInformation()
          * Returned phpinfo page
        */
        public function drawPhpInformation() {
            return phpinfo();
        }


        /*
          * The function for get date by format
          * Usage like drawData('m/d/Y h:i:s a')
          * Input time format
          * Return actual time in your format
        */
        public function drawData($format) {
            return date($format);
        }


        /*
          * The function for get user remote adress
          * Usage like $ip = getRemoteAdress()
          * Return remote adress
        */
        public function getRemoteAdress() {
          if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];
          
          } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];

          } else {
            $address = $_SERVER['REMOTE_ADDR'];
          }
          return $address = "88.103.239.161";
        }



        /*
          * The function for redirect user
          * Usage like redirect("home.php")
          * Input page
        */
        public static function getRootDoc() {
            return $_SERVER['DOCUMENT_ROOT'];
        }


        /*
          * The function for check if is lampp server
          * Usage like $lampp = isLampp();
          * Return true or false
        */
        public function isLampp() {
            if ($this->getRootDoc() == "/opt/lampp/htdocs") {
                return true;
            } else {
                return false;
            }
        }


        /*
          * The function for get protocol
          * Usage like $protocol = getProtocol();
          * Return protocol (http, https)
        */
        public function getProtocol() {
            if (!empty($_SERVER['HTTPS'])) {
                return "https://";
            } else {
                return "http://";
            }
        }


        /*
          * The function for print array
          * Usage like drawArray($array)
          * Input array
        */
        public function drawArray($array) {
            echo '<pre>';
            print_r($array);
            echo '</pre>';
        }


        /*
          * The function for print errors to page
        */
        public function drawErrors() {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);         
        }


        /**
          * Determine if this is a secure HTTPS connection
          * 
          * @return bool True if it is a secure HTTPS connection, otherwise false.
        */
        public function isSSL() {
          if (isset($_SERVER['HTTPS'])) {
              if ($_SERVER['HTTPS'] == 1) {
                  return true;
              } elseif ($_SERVER['HTTPS'] == 'on') {
                  return true;
              }
          }
        
          return false;
        }
    }
?>
