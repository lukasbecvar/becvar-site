<?php

namespace App\Manager;

use App\Entity\Visitor;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban manager provides all ban/unban methods
*/

class BanManager
{
    private $logManager;
    private $authManager;
    private $errorManager;
    private $entityManager;
    private $visitorInfoUtil;
    
    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function getVisitorIP(int $id): string
    {
        $repo = $this->visitorInfoUtil->getVisitorRepositoryByID($id);
        return $repo->getIpAddress();
    }

    public function getBannedCount(): ?int
    {
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            $result = $visitorRepository->findBy(['banned_status' => 'yes']);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        return count($result);
    }

    public function getBanReason(string $ip_address): ?string 
    {
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

    public function isVisitorBanned(string $ip_address): bool 
    {
        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);
        
        // check if visitor found
        if ($visitor === null) {
            return false;
        } else {

            // check if visitor banned
            if ($visitor->getBannedStatus() == 'yes') {
                return true;
            } else {
                return false;
            }
        }
    }

    public function banVisitor(string $ip_address, string $reason): void 
    {
        // get current date
        $date = date('d.m.Y H:i:s');

        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban data
            $visitor->setBannedStatus('yes');
            $visitor->setBanReason($reason);
            $visitor->setBannedTime($date);
            
            // log ban action to database
            $this->logManager->log('ban-system', 'visitor with ip: '.$ip_address.' banned for reason: '.$reason.' by '.$this->authManager->getUsername());

            // update entity data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorManager->handleError('error to ban visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }

    public function unbanVisitor(string $ip_address): void 
    {
        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban status
            $visitor->setBannedStatus('no');
            
            // log ban action to database
            $this->logManager->log('ban-system', 'visitor with ip: '.$ip_address.' unbanned by '.$this->authManager->getUsername());

            // update visitor data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorManager->handleError('error to update ban status of visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }
}
