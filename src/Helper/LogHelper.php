<?php

namespace App\Helper;

use App\Entity\Log;
use App\Manager\CookieManager;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Log helper provides log functions for save events to database table
*/

class LogHelper
{
    private $errorHelper;
    private $securityUtil;
    private $entityManager;
    private $cookieManager;
    private $visitorInfoUtil;
    
    public function __construct(
        ErrorHelper $errorHelper,
        SecurityUtil $securityUtil, 
        CookieManager $cookieManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->errorHelper = $errorHelper;
        $this->securityUtil = $securityUtil;
        $this->cookieManager = $cookieManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function log(string $name, string $value): void 
    {
        // check if logs enabled in config
        if ($this->isLogsEnabled()) {

            // check if antilog is disabled
            if (!$this->isEnabledAntiLog()) {

                // get current date
                $date = date('d.m.Y H:i:s');

                // get visitor browser agent
                $browser = $this->visitorInfoUtil->getBrowser();

                // get visitor ip address
                $ip_address = $this->visitorInfoUtil->getIP();

                // get visitor id
                $visitor_id = $this->visitorInfoUtil->getVisitorID($ip_address);

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
                $LogEntity->setVisitorId($visitor_id);
                
                // try insert row
                try {
                    $this->entityManager->persist($LogEntity);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->errorHelper->handleError('log flush error: '.$e->getMessage(), 500);
                }
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

    public function setAntiLogCookie(): void
    {
        $this->cookieManager->set('anti-log-cookie', $_ENV['ANTI_LOG_COOKIE'], time() + (60*60*24*7*365));
    }

    public function unsetAntiLogCookie(): void
    {
        $this->cookieManager->unset('anti-log-cookie');
    }

    public function isEnabledAntiLog(): bool
    {
        // check if cookie set
        if (isset($_COOKIE['anti-log-cookie'])) {

            // get cookie token
            $token = $this->cookieManager->get('anti-log-cookie');

            // check if token is valid
            if ($token == $_ENV['ANTI_LOG_COOKIE']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } 
}
