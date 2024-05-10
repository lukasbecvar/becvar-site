<?php

namespace App\Controller\Api;

use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\CacheManager;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class VisitorApiController
 *
 * This controller provides API functions for updating visitor status.
 *
 * @package App\Controller\Api
 */
class VisitorApiController extends AbstractController
{
    private LogManager $logManager;
    private CacheManager $cacheManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        LogManager $logManager,
        CacheManager $cacheManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->logManager = $logManager;
        $this->cacheManager = $cacheManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/api/visitor/update/activity', methods: ['GET', 'POST'], name: 'api_visitor_status')]
    public function updateStatus(): Response
    {
        // get user ip
        $ipAddress = $this->visitorInfoUtil->getIP();

        // get visitor repository
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor == null) {
            return $this->json([
                'status' => 'error',
                'message' => 'error visitor not found'
            ], 500);
        }

        // update visitor status
        try {
            // cache online visitor
            $this->cacheManager->setValue('online_user_' . $visitor->getId(), 'online', 300);

            return $this->json([
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            $this->logManager->log('system-error', 'error to update visitor status: ' . $e->getMessage());
            return $this->json([
                'status' => 'error',
                'message' => 'error to update visitor status'
            ], 500);
        }
    }
}
