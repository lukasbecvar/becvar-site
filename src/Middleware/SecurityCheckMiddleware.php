<?php

namespace App\Middleware;

use App\Util\AppUtil;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityCheckMiddleware
 *
 * This middleware checks if the connection is secure
 *
 * @package App\Middleware
 */
class SecurityCheckMiddleware
{
    private AppUtil $appUtil;
    private ErrorManager $errorManager;

    public function __construct(AppUtil $appUtil, ErrorManager $errorManager)
    {
        $this->appUtil = $appUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Check if the connection is secure
     *
     * @return void
     */
    public function onKernelRequest(): void
    {
        // check if SSL check enabled
        if ($this->appUtil->isSSLOnly() && !$this->appUtil->isSsl()) {
            $this->errorManager->handleError(
                'SSL error: connection not running on ssl protocol',
                Response::HTTP_UPGRADE_REQUIRED
            );
        }
    }
}
