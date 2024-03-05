<?php

namespace App\Service\Manager;

use App\Entity\Log;
use App\Util\SiteUtil;
use App\Util\CookieUtil;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthManager
 * 
 * LogManager provides log functions for saving events to a database table.
 * 
 * @package App\Service\Manager
 */
class LogManager
{
    /**
     * @var SiteUtil
     * Instance of the SiteUtil for handling site-related utilities.
     */
    private SiteUtil $siteUtil;

    /**
     * @var CookieUtil
     * Instance of the CookieUtil for handling cookie-related functionality.
     */
    private CookieUtil $cookieUtil;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var VisitorInfoUtil
     * Instance of the VisitorInfoUtil for handling visitor information-related utilities.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * @var EntityManagerInterface
     * Instance of the EntityManagerInterface for interacting with the database.
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * LogManager constructor.
     *
     * @param SiteUtil               $siteUtil
     * @param CookieUtil             $cookieUtil
     * @param ErrorManager           $errorManager
     * @param SecurityUtil           $securityUtil
     * @param VisitorManager         $visitorManager
     * @param VisitorInfoUtil        $visitorInfoUtil
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SiteUtil $siteUtil,
        CookieUtil $cookieUtil,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->cookieUtil = $cookieUtil;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Logs an event.
     *
     * @param string $name
     * @param string $value
     * @param bool $bypass_antilog
     *
     * @return void
     */
    public function log(string $name, string $value, bool $bypass_antilog = false): void 
    {        
        if (str_contains($value, 'Connection refused')) {
            if ($this->siteUtil->isDevMode()) {
                die('Database error: '.$value);
            } else {
                die('Internal server error');
            }
        }

        // check if logs enabled in config
        if (($this->isLogsEnabled() && !$this->isEnabledAntiLog()) || $bypass_antilog) {
            // get log level
            $level = $this->getLogLevel();

            // value character shortifiy
            if (mb_strlen($value) >= 512) {
                $value = mb_substr($value, 0, 512).'...';
            } 

            // disable database log for level 1 & 2
            if ($name == 'database' && $level < 3) {
                return;
            }

            // disable paste, image-uploader log for level 1
            if (($name == 'todo-manager' || $name == 'code-paste' || $name == 'image-uploader' || $name == 'message-sender') && $level < 2) {
                return;
            }

            // get current date
            $date = date('d.m.Y H:i:s');

            // get visitor browser agent
            $browser = $this->visitorInfoUtil->getBrowser();

            // get visitor ip address
            $ip_address = $this->visitorInfoUtil->getIP();

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

    /**
     * Retrieves logs based on IP address.
     *
     * @param string $ip_address
     * @param string $username
     * @param int $page
     *
     * @return Log[]|null
     */
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
            $formated_browser = $this->visitorInfoUtil->getBrowserShortify($user_agent);
            $log->setBrowser($formated_browser);
        }

        return $logs;
    }

    /**
     * Retrieves logs based on status, paginated.
     *
     * @param string $status
     * @param string $username
     * @param int $page
     *
     * @return Log[]|null
     */
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
            $formated_browser = $this->visitorInfoUtil->getBrowserShortify($user_agent);
            $log->setBrowser($formated_browser);
        }

        return $logs;
    }

    /**
     * Retrieves the count of logs based on status.
     *
     * @param string $status
     *
     * @return int
     */
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

    /**
     * Retrieves the count of login logs.
     *
     * @return int
     */
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

    /**
     * Sets the status of all logs to 'readed'.
     *
     * @return void
     */
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

    /**
     * Checks if logs are enabled.
     *
     * @return bool
     */
    public function isLogsEnabled(): bool 
    {
        // check if logs enabled
        if ($_ENV['LOGS_ENABLED'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the anti-log cookie is enabled.
     *
     * @return bool
     */
    public function isEnabledAntiLog(): bool
    {
        // check if cookie set
        if (isset($_COOKIE['anti-log-cookie'])) {

            // get cookie token
            $token = $this->cookieUtil->get('anti-log-cookie');

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

    /**
     * Sets the anti-log cookie.
     *
     * @return void
     */
    public function setAntiLogCookie(): void
    {
        $this->cookieUtil->set('anti-log-cookie', $_ENV['ANTI_LOG_COOKIE'], time() + (60*60*24*7*365));
    }

    /**
     * Unsets the anti-log cookie.
     *
     * @return void
     */
    public function unsetAntiLogCookie(): void
    {
        $this->cookieUtil->unset('anti-log-cookie');
    }

    /**
     * Retrieves the log level from the environment configuration.
     *
     * @return int
     */
    public function getLogLevel(): int
    {
        return $_ENV['LOG_LEVEL'];
    }
}
