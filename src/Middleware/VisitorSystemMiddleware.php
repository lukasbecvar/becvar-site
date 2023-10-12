<?php

namespace App\Middleware;

use Twig\Environment;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Manager\BanManager;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitor system provides basic visitors managment
*/

class VisitorSystemMiddleware
{
    private $twig;
    private $banManager;
    private $logManager;
    private $errorManager;
    private $securityUtil;
    private $entityManager;
    private $visitorInfoUtil;

    public function __construct(
        Environment $twig,
        LogManager $logManager,
        BanManager $banManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager 
    ) {
        $this->twig = $twig;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
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
            if ($this->banManager->isVisitorBanned($ip_address)) {

                // get ban reason 
                $reason = $this->banManager->getBanReason($ip_address);

                // log access to database
                $this->logManager->log('ban-system', 'visitor with ip: '.$ip_address.' trying to access page, but visitor banned for: '.$reason);

                // render banned page
                die($this->twig->render('errors/error-banned.html.twig', [
                    'message' => $reason,
                    'contact_email' => $_ENV['CONTACT_EMAIL']
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
            $this->logManager->log('geolocate-error', 'error to geolocate ip: '.$ip_address);
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
            $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
        }
    }

    public function updateVisitor(string $date, string $ip_address, string $browser, string $os): void
    {
        // get visitor data
        $visitor = $this->visitorInfoUtil->getVisitorRepository($ip_address);

        // check if visitor data found
        if (!$visitor != null) {
            $this->errorManager->handleError('unexpected visitor with ip: '.$ip_address.' update error, please check database structure', 500);
        } else {

            // get current visited_sites value from database
            $visitedSites = intval($visitor->getVisitedSites());

            // update values
            $visitor->setVisitedSites($visitedSites + 1);
            $visitor->setLastVisit($date);
            $visitor->setBrowser($browser);
            $visitor->setOs($os);

            // try to update data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
            }
        }
    }
}
