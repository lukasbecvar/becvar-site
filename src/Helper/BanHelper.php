<?php

namespace App\Helper;

use App\Helper\ErrorHelper;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban helper provides all ban/unban methods
*/

class BanHelper
{

    private $logHelper;
    private $errorHelper;
    private $entityManager;
    private $visitorInfoUtil;
    
    public function __construct(
        LogHelper $logHelper,
        ErrorHelper $errorHelper,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function banVisitor(string $ip_address, string $reason): void {

        // get current date
        $date = date('d.m.Y H:i:s');

        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban data
            $visitor->setBannedStatus('banned');
            $visitor->setBanReason($reason);
            $visitor->setBannedTime($date);
            
            // log ban action to database
            $this->logHelper->log('ban-system', 'visitor with ip: '.$ip_address.' banned for reason: "'.$reason.'" by [[username-here]]');

            // update entity data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorHelper->handleError('error to ban visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }

    public function unbanVisitor(string $ip_address): void {

        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban status
            $visitor->setBannedStatus('un-banned');
            
            // log ban action to database
            $this->logHelper->log('ban-system', 'visitor with ip: '.$ip_address.' unbanned by [[username-here]]');

            // update visitor data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorHelper->handleError('error to update ban status of visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }

    public function getBanReason(string $ip_address): ?string {

        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor == null) {
            return 0;
        } else {

            // return ban reason string
            return $visitor->getBanReason();
        }
    }

    public function isVisitorBanned(string $ip_address): bool {

        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);
        
        // check if visitor found
        if ($visitor === null) {
            return false;
        } else {

            // check if visitor banned
            if ($visitor->getBannedStatus() == 'banned') {
                return true;
            } else {
                return false;
            }
        }
    }
}
