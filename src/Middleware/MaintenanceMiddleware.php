<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;

/**
 * Class MaintenanceMiddleware
 *
 * This middleware is used to check if the application is in maintenance mode.
 */
class MaintenanceMiddleware
{
    /** * @var SiteUtil */
    private SiteUtil $siteUtil;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /**
     * MaintenanceMiddleware constructor.
     *
     * @param SiteUtil     $siteUtil
     * @param ErrorManager $errorManager
     */
    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager)
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Check if the application is in maintenance mode.
     */
    public function onKernelRequest(): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $this->errorManager->handleErrorView('maintenance');            
        }
    }
}
