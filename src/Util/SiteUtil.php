<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Request;

/*
    Site util provides basic site get, set etc methods
*/

class SiteUtil
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    public function getHttpHost(): string
    {
        // return host url
        return Request::createFromGlobals()->getHost();
    }

    public function isRunningLocalhost(): bool 
    {
		$localhost = false;

        // get host url
        $host = SiteUtil::getHttpHost();

        // check if host is null
        if ($host != null) {

            // check if running on url localhost
            if (str_starts_with($host, 'localhost')) {
                $localhost = true;
            } 
                
            // check if running on localhost ip
            if (str_starts_with($host, '127.0.0.1')) {
                $localhost = true;
            }
            
            // check if running on private ip
            if (str_starts_with($host, '10.0.0.93')) {
                $localhost = true;
            }
        }

        return $localhost;
    }

    public function isSsl(): bool 
    {
        // check if set https header
        if (isset($_SERVER['HTTPS'])) {

            // https value (1)
            if ($_SERVER['HTTPS'] == 1) {
                return true;

            // check https value (on)
            } elseif ($_SERVER['HTTPS'] == 'on') {
                return true;
            } else {
                return false;   
            }
        } else {
            return false;   
        }
    }

    public function isMaintenance(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function isDevMode(): bool 
    {
        // check if dev mode enabled in app enviroment
        if ($_ENV['APP_ENV'] == 'dev') {
            return true;
        } else {
            return false;
        }
    }

    public function getQueryString(string $query, Request $request): ?string
    {
        // get query value
        $value = $request->query->get($query);

        // set return to 1 if value is null
        if ($value == null) {
            return '1';
        } else {

            // escape query string value (XSS Protection)
            $output = $this->securityUtil->escapeString($value);
            
            // return final output
            return $output;
        }
    }
}
