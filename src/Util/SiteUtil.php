<?php

namespace App\Util;

/*
    Site util provides basic site get, set etc methods
*/

class SiteUtil
{
    public static function getHttpHost(): ?string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function isRunningLocalhost(): bool 
    {
		$state = false;

        // get http host
        $host = SiteUtil::getHttpHost();
            
        // check if running on url localhost
        if (str_starts_with($host, 'localhost')) {
            $state = true;
        } 
            
        // check if running on localhost ip
        if (str_starts_with($host, '127.0.0.1')) {
            $state = true;
        }
        
        return $state;
    }
}
