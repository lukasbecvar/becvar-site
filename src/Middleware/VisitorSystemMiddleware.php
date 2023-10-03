<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Helper\LogHelper;
use App\Helper\ErrorHelper;
use App\Util\VisitorUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitor system provides basic visitors managment
*/

class VisitorSystemMiddleware
{ 

    private $siteUtil;
    private $logHelper;
    private $errorHelper;
    private $visitorUtil;
    private $securityUtil;
    private $entityManager;

    public function __construct(
        SiteUtil $siteUtil,
        LogHelper $logHelper,
        VisitorUtil $visitorUtil,
        ErrorHelper $errorHelper,
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager 
    ) {
        $this->siteUtil = $siteUtil;
        $this->logHelper = $logHelper;
        $this->visitorUtil = $visitorUtil;
        $this->errorHelper = $errorHelper;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(): void
    {
        // get data to insert
        $date = date('d.m.Y H:i:s');
        $os = $this->visitorUtil->getOS();
        $ipAddress =  $this->visitorUtil->getIP();
        $browser =  $this->visitorUtil->getBrowser();
        $location = $this->getLocation($ipAddress);

        // escape inputs
        $ipAddress = $this->securityUtil->escapeString($ipAddress);
        $browser = $this->securityUtil->escapeString($browser);
        $location = $this->securityUtil->escapeString($location);

        // get visitor ip address
        $address = $this->visitorUtil->getIP();

        // check if visitor found in database
        if (!$this->isVisitorExist($address)) {

            // insert new visitor
            $this->insertNewVisitor($date, $ipAddress, $browser, $os, $location);
        } else {

            // update exist visitor
            $this->updateVisitor($date, $ipAddress, $browser, $os);
        }
    }

    public function insertNewVisitor(string $date, string $ipAddress, string $browser, string $os, string $location): void 
    {
        // visitor entity
        $visitorEntity = new Visitor();

        // set visitor values
        $visitorEntity->setVisitedSites(1);
        $visitorEntity->setFirstVisit($date);
        $visitorEntity->setLastVisit($date);
        $visitorEntity->setBrowser($browser);
        $visitorEntity->setOs($os);
        $visitorEntity->setLocation($location);
        $visitorEntity->setIpAddress($ipAddress);
        $visitorEntity->setBannedStatus('no');
        $visitorEntity->setBanReason('none');
        $visitorEntity->setEmail('unknown');
    
        // set new entity row
        $this->entityManager->persist($visitorEntity);

        // try insert row
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
        }
    }

    public function updateVisitor(string $date, string $ipAddress, string $browser, string $os): void
    {
        // visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class)->findOneBy(['ip_address' => $ipAddress]);

        // check if visitor repo found
        if (!$visitorRepository) {
            $this->errorHelper->handleError('unexpected visitor with ip: '.$ipAddress.' update error, please check database structure', 500);
        } else {

            // get current visited_sites value from database
            $visitedSites = $visitorRepository->getVisitedSites();

            // update values
            $visitorRepository->setVisitedSites($visitedSites + 1);
            $visitorRepository->setLastVisit($date);
            $visitorRepository->setBrowser($browser);
            $visitorRepository->setOs($os);

            $this->entityManager->flush();
        }
    }

    public function isVisitorExist(string $ip_address): bool
    {
        // default state
        $state = false;

        // init entity repository
        $repository = $this->entityManager->getRepository(Visitor::class);
        
        // try find value by column name
        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        // check if found
        if ($result !== null) {
            $state = true;
        } 

        return $state;
    }

    public function getLocation(string $ipAddress): ?string
    {
        $location = null;

        // check if site running on localhost
        if ($this->siteUtil->isRunningLocalhost()) {
            $country = 'HOST';
            $city = 'Location';
        } else {
 
            try {
                // geoplugin url
                $geoplugin_url = $_ENV['GEOPLUGIN_URL'];

                // geoplugin data
                $geoplugin_data = file_get_contents($geoplugin_url.'/json.gp?ip=$ipAddress');

                // decode data
                $details = json_decode($geoplugin_data);
        
                // get country and site from API data
                $country = $details->geoplugin_countryCode;

                // check if city name defined
                if (!empty(explode('/', $details->geoplugin_timezone)[1])) {
                        
                    // get city name from timezone (explode /)
                    $city = explode('/', $details->geoplugin_timezone)[1];
                } else {
                    $city = null;
                }
            } catch (\Exception $e) {

                // set null if data not getted
                $country = null;
                $city = null;

                // log geolocate error
                $this->logHelper->log('geolocate-error', 'error to geolocate ip: ' . $ipAddress . ', error: ' . $e->getMessage());
            }   
        }

        // empty set to null
        if (empty($country)) {
            $country = null;
        }
        if (empty($city)) {
            $city = null;
        }

        // final return
        if  ($country == null or $city == null) {
            $location = 'Unknown';
        } else {
            $location = $country.'/'.$city;
        }

        return $location;
    }
}
