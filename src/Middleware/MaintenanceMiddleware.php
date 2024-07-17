<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use Psr\Log\LoggerInterface;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class MaintenanceMiddleware
 *
 * The middleware for checking the maintenance mode
 *
 * @package App\Middleware
 */
class MaintenanceMiddleware
{
    private SiteUtil $siteUtil;
    private LoggerInterface $logger;
    private ErrorManager $errorManager;

    public function __construct(
        SiteUtil $siteUtil,
        LoggerInterface $loggerInterface,
        ErrorManager $errorManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logger = $loggerInterface;
        $this->errorManager = $errorManager;
    }

    /**
     * Handle the maintenance mode check
     *
     * @param RequestEvent $event The request event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            // handle debug mode exception
            if ($this->siteUtil->isDevMode()) {
                $this->errorManager->handleError(
                    msg: 'the application is under maintenance mode',
                    code: Response::HTTP_SERVICE_UNAVAILABLE
                );
            } else {
                $this->logger->error('the application is under maintenance mode');
            }

            // render the maintenance template
            $content = $this->errorManager->getErrorView('maintenance');
            $response = new Response($content, Response::HTTP_SERVICE_UNAVAILABLE);
            $event->setResponse($response);
        }
    }
}
