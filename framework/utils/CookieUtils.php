<?php // cookie manage utils

    namespace becwork\utils;

    class CookieUtils {

        /*
          * The function for set cookie
          * Usage like cookieSet("TestCookie", 69, time() + (60*60*24*7));
          * Input name value and expiration time in seconds
        */
        public function cookieSet($name, $value, $expiration) {
            setcookie($name, $value, $expiration);
        }

        /*
          * The function for get cookie
          * Usage like $cookie = getCookie("cookieName")
          * Input cookie name
          * Return cookie value
        */
        public function getCookie($name) {
            return $_COOKIE[$name];
        }

        /*
          * The function for unset cookie by name
          * Usage like unset_cookie("name");
          * Input cookie name (string)
        */
        public function unset_cookie($name) {
            
            // get http host
            $host = $_SERVER['HTTP_HOST'];
            
            // explode host to array
            $domain = explode(':', $host)[0];

            // get request uri
            $uri = $_SERVER['REQUEST_URI'];
            
            // valudate uri
            $uri = rtrim(explode('?', $uri)[0], '/');

            // exeption handle
            if ($uri && !filter_var('file://' . $uri, FILTER_VALIDATE_URL)) {
                throw new Exception('invalid uri: ' . $uri);
            }

            // explode parts array
            $parts = explode('/', $uri);

            // default cookie path
            $cookiePath = '';
            
            foreach ($parts as $part) {
                
                // cookie path builder
                $cookiePath = '/'.ltrim($cookiePath.'/'.$part, '//');

                // set cookie whit minimal time
                setcookie($name, '', 1, $cookiePath);

                // set cookie whit minimal time and domain
                do {
                    setcookie($name, '', 1, $cookiePath, $domain);
                } while (strpos($domain, '.') !== false && $domain = substr($domain, 1 + strpos($domain, '.')));
            }
        }

        /*
          * The function for print cookie array
          * Usage like printCookie()
        */
        public function printCookie() {
            print_r($_COOKIE);
        }
    }
?>
