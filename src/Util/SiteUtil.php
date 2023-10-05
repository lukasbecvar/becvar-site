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
}
