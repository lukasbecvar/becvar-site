<?php

namespace App\Util;

use App\Util\SecurityUtil;

/**
 * CookieUtil provides cookie management functionalities.
 */
class CookieUtil
{
    /** @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /**
     * CookieUtil constructor.
     *
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Set a cookie with the specified name, value, and expiration.
     *
     * @param string $name The name of the cookie.
     * @param mixed $value The value to store in the cookie.
     * @param int $expiration The expiration time for the cookie.
     *
     * @throws \Exception If headers have already been sent.
     */
    public function set($name, $value, $expiration): void 
    {
        if (!headers_sent()) {
            $value = $this->securityUtil->encryptAes($value);
            $value = base64_encode($value);
            setcookie($name, $value, $expiration, '/');
        }
    }

    /**
     * Get the value of the specified cookie.
     *
     * @param string $name The name of the cookie.
     *
     * @return string|null The decrypted value of the cookie.
     */
    public function get($name): ?string 
    {
        $value = base64_decode($_COOKIE[$name]);
        return $this->securityUtil->decryptAes($value);
    }

    /**
     * Unset (delete) the specified cookie.
     *
     * @param string $name The name of the cookie.
     *
     * @throws \Exception If the URI is invalid.
     */
    public function unset($name): void 
    {   
        if (!headers_sent()) {
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
}
