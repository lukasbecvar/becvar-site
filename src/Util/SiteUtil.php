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
        $request = Request::createFromGlobals();
        return $request->getHost();
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
        
        return $localhost;
    }

    public function isSsl(): bool 
    {
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
