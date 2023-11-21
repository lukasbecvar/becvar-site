<?php

namespace App\Controller\Api;

use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: update visitor status to online
*/

class VisitorApiController extends AbstractController
{
    private LogManager $logManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        LogManager $logManager, 
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->logManager = $logManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/api/visitor/update/activity', name: 'api_visitor_status')]
    public function updateStatus(EntityManagerInterface $entityManager): Response
    {
        // get user ip
        $ip_address = $this->visitorInfoUtil->getIP();

        // get visitor repository
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {
            // update visitor status
            $visitor->setStatusUpdateTime(time());
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
