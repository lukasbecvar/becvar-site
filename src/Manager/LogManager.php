<?php

namespace App\Manager;

use App\Entity\Log;
use App\Util\SecurityUtil;
use App\Manager\CookieManager;
use Doctrine\ORM\EntityManagerInterface;

/*
    Log manager provides log functions for save events to database table
*/

class LogManager
{
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private CookieManager $cookieManager;
    private VisitorManager $visitorManager;
    private EntityManagerInterface $entityManager;
    
    public function __construct(
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        CookieManager $cookieManager,
        VisitorManager $visitorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->cookieManager = $cookieManager;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
    }

    public function log(string $name, string $value): void 
    {
        // check if logs enabled in config
        if ($this->isLogsEnabled()) {

            // check if antilog is disabled
            if (!$this->isEnabledAntiLog()) {

                // get log level
                $level = $this->getLogLevel();

                // disable database log for level 1 & 2
                if ($name == 'database' && $level < 3) {
                    return;
                }

                // disable paste, image-uploader log for level 1
                if (($name == 'code-paste' || $name == 'image-uploader' || $name == 'message-sender') && $level < 2) {
                    return;
                }

                // get current date
                $date = date('d.m.Y H:i:s');

                // get visitor browser agent
                $browser = $this->visitorManager->getBrowser();

                // get visitor ip address
                $ip_address = $this->visitorManager->getIP();

                // get visitor id
                $visitor_id = strval($this->visitorManager->getVisitorID($ip_address));

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
                $LogEntity->setTime($date); 
                $LogEntity->setIpAddress($ip_address); 
                $LogEntity->setBrowser($browser); 
                $LogEntity->setStatus('unreaded'); 
                $LogEntity->setVisitorId($visitor_id);
                
                // try insert row
                try {
                    $this->entityManager->persist($LogEntity);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->errorManager->handleError('log flush error: '.$e->getMessage(), 500);
                }
            }
        }
    }

    public function getLogsWhereIP(string $ip_address, $username, int $page): ?array
    {
        $repo = $this->entityManager->getRepository(Log::class);
        $per_page = $_ENV['ITEMS_PER_PAGE'];
        
        // calculate offset
        $offset = ($page - 1) * $per_page;
    
        // get logs from database
        try {
            $queryBuilder = $repo->createQueryBuilder('l')
                ->where('l.ip_address = :ip_address')
                ->orderBy('l.id', 'DESC')
                ->setParameter('ip_address', $ip_address)
                ->setFirstResult($offset)  
                ->setMaxResults($per_page);
    
            $logs = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get logs: ' . $e->getMessage(), 500);
            $logs = [];
        }
    
        $this->log('database', 'user: ' . $username . ' viewed logs');

        // replace browser with formated value for log reader
        foreach ($logs as $log) {
            $user_agent = $log->getBrowser();
            $formated_browser = $this->visitorManager->getBrowserShortify($user_agent);
            $log->setBrowser($formated_browser);
        }

        return $logs;
    }

    public function getLogs(string $status, $username, int $page): ?array
    {
        $repo = $this->entityManager->getRepository(Log::class);
        $per_page = $_ENV['ITEMS_PER_PAGE'];
        
        // calculate offset
        $offset = ($page - 1) * $per_page;
    
        // get logs from database
        try {
            $queryBuilder = $repo->createQueryBuilder('l')
                ->where('l.status = :status')
                ->orderBy('l.id', 'DESC')
                ->setParameter('status', $status)
                ->setFirstResult($offset)  
                ->setMaxResults($per_page);
    
            $logs = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get logs: ' . $e->getMessage(), 500);
            $logs = [];
        }
    
        $this->log('database', 'user: ' . $username . ' viewed logs');

        // replace browser with formated value for log reader
        foreach ($logs as $log) {
            $user_agent = $log->getBrowser();
            $formated_browser = $this->visitorManager->getBrowserShortify($user_agent);
            $log->setBrowser($formated_browser);
        }

        return $logs;
    }

    public function getLogsCount(string $status): int
    {
        $repo = $this->entityManager->getRepository(Log::class);

        try {
            $logs = $repo->findBy(['status' => $status]);   
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get logs: ' . $e->getMessage(), 500);
            $logs = [];
        } 

        return count($logs);
    }

    public function getLoginLogsCount(): int
    {
        $repo = $this->entityManager->getRepository(Log::class);

        try {
            $logs = $repo->findBy(['name' => 'authenticator']);   
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get logs: ' . $e->getMessage(), 500);
            $logs = [];
        } 

        return count($logs);
    }

    public function setReaded(): void
    {
        $dql = "UPDATE App\Entity\Log l SET l.status = 'readed'";

        try {
            $query = $this->entityManager->createQuery($dql);
            $query->execute();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to set readed logs: '.$e->getMessage(), 500);
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

    public function setAntiLogCookie(): void
    {
        $this->cookieManager->set('anti-log-cookie', $_ENV['ANTI_LOG_COOKIE'], time() + (60*60*24*7*365));
    }

    public function unsetAntiLogCookie(): void
    {
        $this->cookieManager->unset('anti-log-cookie');
    }

    public function getLogLevel(): int
    {
        return $_ENV['LOG_LEVEL'];
    }
}
