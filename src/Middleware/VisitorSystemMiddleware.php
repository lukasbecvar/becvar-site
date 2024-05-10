<?php

namespace App\Middleware;

use Twig\Environment;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Manager\BanManager;
use App\Manager\CacheManager;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ErrorManager;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class VisitorSystemMiddleware
 *
 * Visitor system provides basic visitors managment
 *
 * @package App\Middleware
 */
class VisitorSystemMiddleware
{
    private Environment $twig;
    private BanManager $banManager;
    private LogManager $logManager;
    private CacheManager $cacheManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Environment $twig,
        LogManager $logManager,
        BanManager $banManager,
        CacheManager $cacheManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->twig = $twig;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
        $this->cacheManager = $cacheManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Handles actions to be performed on each kernel request.
     *
     * - Updates visitors' statistics.
     * - Retrieves and sanitizes visitor information.
     * - Checks if the visitor is in the database and takes appropriate actions.
     *
     * @return void
     */
    public function onKernelRequest(): void
    {
        // get data to insert
        $date = date('d.m.Y H:i');
        $os = $this->visitorInfoUtil->getOS();
        $ip_address = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowser();

        // escape inputs
        $ip_address = $this->securityUtil->escapeString($ip_address);
        $browser = $this->securityUtil->escapeString($browser);

        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found in database
        if ($visitor == null) {
            // save new visitor data
            $this->insertNewVisitor($date, $ip_address, $browser, $os);
        } else {
            // cache online visitor
            $this->cacheManager->setValue('online_user_' . $visitor->getId(), 'online', 300);

            // check if visitor banned
            if ($this->banManager->isVisitorBanned($ip_address)) {
                $reason = $this->banManager->getBanReason($ip_address);
                $this->logManager->log('ban-system', 'visitor with ip: ' . $ip_address . ' trying to access page, but visitor banned for: ' . $reason);

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

    /**
     * Inserts a new visitor record into the database.
     *
     * @param string $date The date of the visit.
     * @param string $ip_address The IP address of the visitor.
     * @param string $browser The browser used by the visitor.
     * @param string $os The operating system of the visitor.
     *
     * @throws \Exception If an error occurs during the database flush.
     */
    public function insertNewVisitor(string $date, string $ip_address, string $browser, string $os): void
    {
        // get visitor ip address
        $location = $this->visitorInfoUtil->getLocation($ip_address);

        // log geolocate error
        if ($location['city'] == 'Unknown' || $location['country'] == 'Unknown') {
            $this->logManager->log('geolocate-error', 'error to geolocate ip: ' . $ip_address);
        }

        // prevent maximal user agent length
        if (strlen($browser) >= 200) {
            $browser = substr($browser, 0, 197) . "...";
        }

        // create new visitor entity
        $visitorEntity = new Visitor();

        // set visitor data
        $visitorEntity->setFirstVisit($date);
        $visitorEntity->setLastVisit($date);
        $visitorEntity->setBrowser($browser);
        $visitorEntity->setOs($os);
        $visitorEntity->setCity($location['city']);
        $visitorEntity->setCountry($location['country']);
        $visitorEntity->setIpAddress($ip_address);
        $visitorEntity->setBannedStatus('no');
        $visitorEntity->setBanReason('non-banned');
        $visitorEntity->setBannedTime(('non-banned'));
        $visitorEntity->setEmail('unknown');

        // try to insert new visitor
        try {
            $this->entityManager->persist($visitorEntity);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError('flush error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Updates an existing visitor record in the database.
     *
     * @param string $date The date of the visit.
     * @param string $ip_address The IP address of the visitor.
     * @param string $browser The updated browser used by the visitor.
     * @param string $os The updated operating system of the visitor.
     *
     * @throws \Exception If an error occurs during the database flush.
     */
    public function updateVisitor(string $date, string $ip_address, string $browser, string $os): void
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // prevent maximal useragent to save
        if (strlen($browser) >= 200) {
            $browser = substr($browser, 0, 197) . "...";
        }

        // check if visitor data found
        if (!$visitor != null) {
            $this->errorManager->handleError('unexpected visitor with ip: ' . $ip_address . ' update error, please check database structure', 500);
        } else {
            // update values
            $visitor->setLastVisit($date);
            $visitor->setBrowser($browser);
            $visitor->setOs($os);

            // try to update data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: ' . $e->getMessage(), 500);
            }
        }
    }
}
