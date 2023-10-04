<?php

namespace App\Middleware;

use Twig\Environment;
use App\Entity\Visitor;
use App\Helper\BanHelper;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Helper\ErrorHelper;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitor system provides basic visitors managment
*/

class VisitorSystemMiddleware
{ 

    private $twig;
    private $banHelper;
    private $logHelper;
    private $errorHelper;
    private $securityUtil;
    private $entityManager;
    private $visitorInfoUtil;

    public function __construct(
        Environment $twig,
        LogHelper $logHelper,
        BanHelper $banHelper,
        ErrorHelper $errorHelper,
        SecurityUtil $securityUtil,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager 
    ) {
        $this->twig = $twig;
        $this->banHelper = $banHelper;
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function onKernelRequest(): void
    {
        // get data to insert
        $date = date('d.m.Y H:i:s');
        $os = $this->visitorInfoUtil->getOS();
        $ip_address = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowser();
        $location = $this->visitorInfoUtil->getLocation($ip_address);

        // escape inputs
        $ip_address = $this->securityUtil->escapeString($ip_address);
        $browser = $this->securityUtil->escapeString($browser);
        $location = $this->securityUtil->escapeString($location);

        // check if visitor found in database
        if ($this->visitorInfoUtil->getVisitorRepository($ip_address) == null) {

            // save new visitor data
            $this->insertNewVisitor($date, $ip_address, $browser, $os, $location);
        } else {

            // check if visitor banned
            if ($this->banHelper->isVisitorBanned($ip_address)) {

                // get ban reason 
                $reason = $this->banHelper->getBanReason($ip_address);

                // log access to database
                $this->logHelper->log('ban-system', 'visitor with ip: '.$ip_address.' trying to access page, but visitor banned for: '.$reason);

                // render banned page
                die($this->twig->render('errors/error-banned.html.twig', 
                    ['message' => $reason
                ]));

            } else {   
                // update exist visitor
                $this->updateVisitor($date, $ip_address, $browser, $os);
            }
        }
    }

    public function insertNewVisitor(string $date, string $ip_address, string $browser, string $os, string $location): void 
    {
        
        // log geolocate error
        if ($location == 'Unknown') {
            $this->logHelper->log('geolocate-error', 'error to geolocate ip: '.$ip_address);
        }

        // create new visitor entity
        $visitorEntity = new Visitor();

        // set visitor data
        $visitorEntity->setVisitedSites(1);
        $visitorEntity->setFirstVisit($date);
        $visitorEntity->setLastVisit($date);
        $visitorEntity->setBrowser($browser);
        $visitorEntity->setOs($os);
        $visitorEntity->setLocation($location);
        $visitorEntity->setIpAddress($ip_address);
        $visitorEntity->setBannedStatus('non-banned');
        $visitorEntity->setBanReason('non-banned');
        $visitorEntity->setBannedTime(('non-banned'));
        $visitorEntity->setEmail('unknown');
            
        // insert new visitor
        try {
            $this->entityManager->persist($visitorEntity);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
        }
    }

    public function updateVisitor(string $date, string $ip_address, string $browser, string $os): void
    {
        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor data found
        if (!$visitor != null) {
            $this->errorHelper->handleError('unexpected visitor with ip: '.$ip_address.' update error, please check database structure', 500);
        } else {

            // get current visited_sites value from database
            $visitedSites = $visitor->getVisitedSites();

            // update values
            $visitor->setVisitedSites($visitedSites + 1);
            $visitor->setLastVisit($date);
            $visitor->setBrowser($browser);
            $visitor->setOs($os);

            // try to update data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
            }
        }
    }
}
