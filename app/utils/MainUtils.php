<?php // main utils

    namespace becwork\utils;

    class MainUtils { 

        /*
          * The function for get user remote adress
          * Usage like $ip = get_remote_adress()
          * Return remote adress
        */
        public function get_remote_adress(): ?string {
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
        public static function get_root_doc(): ?string {
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            return $doc_root;
        }

        /*
          * The function for get protocol
          * Usage like $protocol = get_protocol();
          * Return protocol (http, https)
        */
        public function get_protocol(): ?string {

            // default protocol
            $protocol = "http://";

            // check if https
            if (!empty($_SERVER['HTTPS'])) {
                $protocol = "https://";
            }

            return $protocol;
        }

        /*
          * The function for print errors to page
        */
        public function enable_errors(): void {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);         
        }

        /**
          * Determine if this is a secure HTTPS connection
          * 
          * @return bool True if it is a secure HTTPS connection, otherwise false.
        */
        public function is_ssl(): bool {

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