<?php // main utils

    namespace becwork\utils;

    class MainUtils { 

        /*
          * The function for get php server infromation
          * Usage like echo drawPhpInformation()
          * Returned phpinfo page
        */
        public function drawPhpInformation(): void {
            phpinfo();
        }

        /*
          * The function for get date by format
          * Usage like drawData('m/d/Y h:i:s a')
          * Input time format
          * Return actual time in your format
        */
        public function drawData($format): ?string {
            $date = date($format);
            return $data;
        }

        /*
          * The function for get user remote adress
          * Usage like $ip = getRemoteAdress()
          * Return remote adress
        */
        public function getRemoteAdress(): ?string {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $address = $_SERVER['HTTP_CLIENT_IP'];
          
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $address = $_SERVER['HTTP_X_FORWARDED_FOR'];

            } else {
                $address = $_SERVER['REMOTE_ADDR'];
            }
            return $address;
        }

        /*
          * The function for redirect user
          * Usage like redirect("home.php")
          * Input page
        */
        public static function getRootDoc(): ?string {
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            return $doc_root;
        }

        /*
          * The function for check if is lampp server
          * Usage like $lampp = isLampp();
          * Return true or false
        */
        public function isLampp(): bool {

            // default state
            $state = false;

            if ($this->getRootDoc() == "/opt/lampp/htdocs") {
                $state = true;
            }

            return $state;
        }

        /*
          * The function for get protocol
          * Usage like $protocol = getProtocol();
          * Return protocol (http, https)
        */
        public function getProtocol(): ?string {

            // default protocol
            $protocol = "http://";

            // check if https
            if (!empty($_SERVER['HTTPS'])) {
                $protocol = "https://";
            }

            return $protocol;
        }

        /*
          * The function for print array
          * Usage like drawArray($array)
          * Input array
        */
        public function drawArray($array): void {
            echo '<pre>';
            print_r($array);
            echo '</pre>';
        }

        /*
          * The function for print errors to page
        */
        public function drawErrors(): void {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);         
        }

        /**
          * Determine if this is a secure HTTPS connection
          * 
          * @return bool True if it is a secure HTTPS connection, otherwise false.
        */
        public function isSSL(): bool {

            // default state
            $state = false;

            // ssl check
            if (isset($_SERVER['HTTPS'])) {
                if ($_SERVER['HTTPS'] == 1) {
                    $state = true;
                } elseif ($_SERVER['HTTPS'] == 'on') {
                    $state = true;
                }
            }
        
            return $state;
        }
    }
?>
