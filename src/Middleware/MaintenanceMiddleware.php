<?php

namespace App\Middleware;

use App\Helper\ErrorHelper;
use App\Util\SiteUtil;

/*
    This middleware used to check if is application in devmode
*/

class MaintenanceMiddleware
{
    private $siteUtil;
    private $errorHelper;

    public function __construct(SiteUtil $siteUtil, ErrorHelper $errorHelper)
    {
        $this->siteUtil = $siteUtil;
        $this->errorHelper = $errorHelper;
    }

    public function onKernelRequest(): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $this->errorHelper->handleErrorView('maintenance');            
        }
    }
}
