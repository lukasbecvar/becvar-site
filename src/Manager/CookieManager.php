<?php

namespace App\Manager;

/*
    Cookie manager provides cookies managment
*/

class CookieManager
{
    public static function set($name, $value, $expiration): void 
    {
        setcookie($name, $value, $expiration);
    }

    public static function get($name): ?string 
    {
        return $_COOKIE[$name];
    }

    public static function unset($name): void 
    {   
        $host = $_SERVER['HTTP_HOST'];   
        $domain = explode(':', $host)[0];
        $uri = $_SERVER['REQUEST_URI'];
        $uri = rtrim(explode('?', $uri)[0], '/');

        if ($uri && !filter_var('file://' . $uri, FILTER_VALIDATE_URL)) {
            throw new \Exception('invalid uri: ' . $uri);
        }

        $parts = explode('/', $uri);
        $cookie_path = '';
            
        foreach ($parts as $part) {  
            $cookie_path = '/'.ltrim($cookie_path.'/'.$part, '//');
            setcookie($name, '', 1, $cookie_path);
             do {
                setcookie($name, '', 1, $cookie_path, $domain);
            } while (strpos($domain, '.') !== false && $domain = substr($domain, 1 + strpos($domain, '.')));
        }
    }
}
