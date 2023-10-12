<?php

namespace App\Middleware;

use App\Manager\ErrorManager;
use App\Util\SiteUtil;

/*
    This middleware used to check if is application in devmode
*/

class MaintenanceMiddleware
{
    private $siteUtil;
    private $errorManager;

    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager)
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    public function onKernelRequest(): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $this->errorManager->handleErrorView('maintenance');            
        }
    }
}
