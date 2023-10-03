<?php

namespace App\Helper;

use App\Entity\Log;
use App\Util\EscapeUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Log helper provides log functions for save events to database table
*/

class LogHelper
{

    private $errorHelper;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ErrorHelper $errorHelper)
    {
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
    }

    public function log(string $name, string $value): void 
    {

        // check if logs enabled in config
        if ($this->isLogsEnabled()) {

            // get current date
            $date = date('d.m.Y H:i:s');

            // get visitor browser agent
            $browser = VisitorInfoUtil::getBrowser();

            // get visitor ip address
            $ip_address = VisitorInfoUtil::getIP();

            // xss escape inputs
            $name = EscapeUtil::special_chars_strip($name);
            $value = EscapeUtil::special_chars_strip($value);
            $browser = EscapeUtil::special_chars_strip($browser);
            $ip_address = EscapeUtil::special_chars_strip($ip_address);
            
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
