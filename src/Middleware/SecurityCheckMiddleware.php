<?php

namespace App\Middleware;

use App\Manager\ErrorManager;
use App\Util\SiteUtil;

/*
    This middleware check if connection is secure
*/

class SecurityCheckMiddleware
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
        // check if app not localhost running
        if (!$this->siteUtil->isRunningLocalhost()) {

            // check SSL
            if (!$this->siteUtil->isSsl()) {
                $this->errorManager->handleError('SSL error: connection not running on ssl protocol', 500);
            }
        }
    }
}