<?php

namespace App\Controller\Api;

use App\Util\SiteUtil;
use App\Util\CacheUtil;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class VisitorApiController
 *
 * This controller provides API functions for updating visitor status
 *
 * @package App\Controller\Api
 */
class VisitorApiController extends AbstractController
{
    private SiteUtil $siteUtil;
    private CacheUtil $cacheUtil;
    private LogManager $logManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        CacheUtil $cacheUtil,
        LogManager $logManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->cacheUtil = $cacheUtil;
        $this->logManager = $logManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * API endpoint for updating visitor status
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/api/visitor/update/activity', methods: ['GET', 'POST'], name: 'api_visitor_status')]
    public function updateStatus(): Response
    {
        // check if updating visitor status from host server
        if ($this->siteUtil->getHostServerIpAddress() == $this->visitorInfoUtil->getIP()) {
            return $this->json([
                'status' => 'error',
                'message' => 'error cannot update visitor status from host server'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // get user ip
        $ipAddress = $this->visitorInfoUtil->getIP();

        // get visitor repository
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor == null) {
            return $this->json([
                'status' => 'error',
                'message' => 'error visitor not found'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // update visitor status
        try {
            // cache online visitor
            $this->cacheUtil->setValue('online_user_' . $visitor->getId(), 'online', 400);

            // update visitor status
            return $this->json([
                'status' => 'success'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // log error
            $this->logManager->log(
                name: 'system-error',
                value: 'error to update visitor status: ' . $e->getMessage()
            );

            // return error
            return $this->json([
                'status' => 'error',
                'message' => 'error to update visitor status'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
