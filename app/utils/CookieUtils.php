<?php // cookie manage utils

    namespace becwork\utils;

    class CookieUtils {

        /*
          * The function for set cookie
          * Usage like set("TestCookie", 69, time() + (60*60*24*7));
          * Input name value and expiration time in seconds
        */
        public function set($name, $value, $expiration): void {
            setcookie($name, $value, $expiration);
        }

        /*
          * The function for get cookie
          * Usage like $cookie = get("cookieName")
          * Input cookie name
          * Return cookie value
        */
        public function get($name): ?string {
            return $_COOKIE[$name];
        }

        /*
          * The function for unset cookie by name
          * Usage like unset("name");
          * Input cookie name (string)
        */
        public function unset($name): void {
            
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
            $cookie_path = '';
            
            foreach ($parts as $part) {
                
                // cookie path builder
                $cookie_path = '/'.ltrim($cookie_path.'/'.$part, '//');

                // set cookie whit minimal time
                setcookie($name, '', 1, $cookie_path);

                // set cookie whit minimal time and domain
                do {
                    setcookie($name, '', 1, $cookie_path, $domain);
                } while (strpos($domain, '.') !== false && $domain = substr($domain, 1 + strpos($domain, '.')));
            }
        }
    }
?>