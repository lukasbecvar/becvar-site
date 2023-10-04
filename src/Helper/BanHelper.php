<?php

namespace App\Helper;

use App\Entity\Visitor;
use App\Helper\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban helper provides all ban/unban methods
*/

class BanHelper
{

    private $logHelper;
    private $errorHelper;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper,
        ErrorHelper $errorHelper,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
    }

    public function banVisitor(string $ip_address, string $reason) {

        // get current date
        $date = date('d.m.Y H:i:s');

        $repository = $this->entityManager->getRepository(Visitor::class);

        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        if ($result !== null) {

            // update ban data
            $result->setBannedStatus('banned');
            $result->setBanReason($reason);
            $result->setBannedTime($date);
            
            // log ban action to database
            $this->logHelper->log('ban-system', 'visitor with ip: '.$ip_address.' banned for reason: "'.$reason.'" by [[username-here]]');

            // update visitor ban status
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorHelper->handleError('error to ban visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }

    }

    public function unbanVisitor(string $ip_address) {

        $repository = $this->entityManager->getRepository(Visitor::class);

        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        if ($result !== null) {

            // update ban status
            $result->setBannedStatus('un-banned');
            
            // log ban action to database
            $this->logHelper->log('ban-system', 'visitor with ip: '.$ip_address.' unbanned by [[username-here]]');

            // update visitor ban status
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

        $repository = $this->entityManager->getRepository(Visitor::class);

        // try to get visitor data
        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        if ($result === null) {
            return 0;
        } else {
            return $result->getBanReason();
        }

    }

    public function isVisitorBanned(string $ip_address) {

        $repository = $this->entityManager->getRepository(Visitor::class);
        
        // get visitor data
        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }
        
        // check if data found
        if ($result === null) {
            return false;
        } else {

            // check if user banned
            if ($result->getBannedStatus() == 'banned') {
                return true;
            } else {
                return false;
            }
        }

    }
}
