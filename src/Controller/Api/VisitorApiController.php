<?php

namespace App\Controller\Api;

use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var VisitorInfoUtil
     * Instance of the VisitorInfoUtil for handling visitor information-related utilities.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * VisitorApiController constructor.
     *
     * @param LogManager      $logManager
     * @param VisitorManager  $visitorManager
     * @param VisitorInfoUtil $visitorInfoUtil
     */
    public function __construct(
        LogManager $logManager, 
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->logManager = $logManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * API endpoint for updating the status of a visitor.
     *
     * @param EntityManagerInterface $entityManager The EntityManager for database operations.
     * @return Response Returns a Response indicating the success or failure of the visitor status update.
     *
     * @throws \Exception Throws an exception if there is an error during the visitor status update.
     */
    #[Route('/api/visitor/update/activity', methods: ['GET', 'POST'], name: 'api_visitor_status')]
    public function updateStatus(EntityManagerInterface $entityManager): Response
    {
        // get user ip
        $ip_address = $this->visitorInfoUtil->getIP();

        // get visitor repository
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {
            // update visitor status 
            $visitor->setStatusUpdateTime(strval(time()));
            $visitor->setStatus('online');

            // update visitor status
            try {
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->logManager->log('system-error', 'error to update visitor status: '.$e->getMessage());
                return $this->json([
                    'status' => 'error',
                    'message' => 'error to update visitor status'
                ], 500);
            }
    
            return $this->json([
                'status' => 'success'
            ], 200);
        } else {
            return $this->json([
                'status' => 'error',
                'message' => 'error visitor not found'
            ], 500);
        }
    }
}
