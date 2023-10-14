<?php

namespace App\Manager;

use App\Util\SecurityUtil;

/*
    Cookie manager provides cookies managment
*/

class CookieManager
{
    private $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    public function set($name, $value, $expiration): void 
    {
        $value = $this->securityUtil->encrypt_aes($value);
        $value = base64_encode($value);
        setcookie($name, $value, $expiration, '/');
    }

    public function get($name): ?string 
    {
        $value = base64_decode($_COOKIE[$name]);
        return $this->securityUtil->decrypt_aes($value);
    }

    public function unset($name): void 
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
