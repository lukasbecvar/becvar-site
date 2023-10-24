<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Request;

/*
    Site util provides basic site get, set etc methods
*/

class SiteUtil
{
    public function getHttpHost(): ?string
    {
        return Request::createFromGlobals()->getHost();
    }

    public function isRunningLocalhost(): bool 
    {
		$localhost = false;

        $host = SiteUtil::getHttpHost();

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

        return $localhost;
    }

    public function isSsl(): bool 
    {
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 1) {
                return true;
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
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function isDevMode(): bool 
    {
        if ($_ENV['APP_ENV'] == 'dev') {
            return true;
        } else {
            return false;
        }
    }
}
