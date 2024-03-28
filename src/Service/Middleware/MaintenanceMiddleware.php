<?php

namespace App\Service\Middleware;

use App\Util\SiteUtil;
use App\Service\Manager\ErrorManager;

/**
 * Class MaintenanceMiddleware
 *
 * This middleware is used to check if the application is in maintenance mode.
 * 
 * @package App\Service\Middleware
 */
class MaintenanceMiddleware
{
    /**
     * @var SiteUtil
     * Instance of the SiteUtil for handling site-related utilities.
     */
    private SiteUtil $siteUtil;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
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
        $this->errorManager->handleError('maintenance', 500);  

        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $this->errorManager->handleErrorView('maintenance');            
        }
    }
}
