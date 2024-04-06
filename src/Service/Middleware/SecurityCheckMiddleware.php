<?php

namespace App\Service\Middleware;

use App\Util\SiteUtil;
use App\Service\Manager\ErrorManager;

/**
 * Class SecurityCheckMiddleware
 *
 * This middleware checks if the connection is secure.
 * 
 * @package App\Service\Middleware
 */
class SecurityCheckMiddleware
{
    private SiteUtil $siteUtil;
    private ErrorManager $errorManager;

    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager)
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Check if the connection is secure.
     */
    public function onKernelRequest(): void
    {
        // check if SSL check enabled
        if ($this->siteUtil->isSSLOnly()) {
            if (!$this->siteUtil->isSsl()) {
                $this->errorManager->handleError('SSL error: connection not running on ssl protocol', 500);
            }
        }
    }
}
