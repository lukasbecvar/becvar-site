<?php

namespace App\Helper;

use App\Entity\Log;
use App\Util\SecurityUtil;
use App\Util\VisitorUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Log helper provides log functions for save events to database table
*/

class LogHelper
{

    private $visitorUtil;
    private $errorHelper;
    private $securityUtil;
    private $entityManager;

    public function __construct(
        VisitorUtil $visitorUtil,
        ErrorHelper $errorHelper,
        SecurityUtil $securityUtil, 
        EntityManagerInterface $entityManager
    ) {
        $this->visitorUtil = $visitorUtil;
        $this->errorHelper = $errorHelper;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    public function log(string $name, string $value): void 
    {

        // check if logs enabled in config
        if ($this->isLogsEnabled()) {

            // get current date
            $date = date('d.m.Y H:i:s');

            // get visitor browser agent
            $browser = $this->visitorUtil->getBrowser();

            // get visitor ip address
            $ip_address = $this->visitorUtil->getIP();

            // xss escape inputs
            $name = $this->securityUtil->escapeString($name);
            $value = $this->securityUtil->escapeString($value);
            $browser = $this->securityUtil->escapeString($browser);
            $ip_address = $this->securityUtil->escapeString($ip_address);
            
            // create new log enity
            $LogEntity = new Log();

            // set log entity values
            $LogEntity->setName($name); 
            $LogEntity->setValue($value); 
            $LogEntity->setDate($date); 
            $LogEntity->setIpAddress($ip_address); 
            $LogEntity->setBrowser($browser); 
            $LogEntity->setStatus('unreaded'); 
            
            // try insert row
            try {
                $this->entityManager->persist($LogEntity);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
            }
        }
    }

    public function isLogsEnabled(): bool 
    {
        // check if logs enabled
        if ($_ENV['LOGS_ENABLED'] == 'true') {
            return true;
        } else {
            return false;
        }
    }
}
