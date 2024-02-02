<?php

namespace App\Middleware;

use Twig\Environment;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Manager\BanManager;
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
    /**
     * @var Environment
     * Instance of the Twig\Environment for rendering templates with Twig.
     */
    private Environment $twig;

    /**
     * @var BanManager
     * Instance of the BanManager for handling ban-related functionality.
     */
    private BanManager $banManager;

    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

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
     * VisitorSystemMiddleware Constructor.
     *
     * @param Environment            $twig               The Twig environment for rendering templates.
     * @param LogManager             $logManager         The log manager for handling log-related tasks.
     * @param BanManager             $banManager         The manager for handling user bans.
     * @param ErrorManager           $errorManager       The manager for handling errors.
     * @param SecurityUtil           $securityUtil       The utility class for security-related tasks.
     * @param VisitorManager         $visitorManager     The manager for handling visitors.
     * @param VisitorInfoUtil        $visitorInfoUtil    The utility class for retrieving visitor information.
     * @param EntityManagerInterface $entityManager      The Doctrine EntityManager for database interactions.
     */
    public function __construct(
        Environment $twig,
        LogManager $logManager,
        BanManager $banManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager 
    ) {
        $this->twig = $twig;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
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
        // update visitors stats list
        $this->visitorManager->updateVisitorsStatus();

        // get data to insert
        $date = date('d.m.Y H:i');
        $os = $this->visitorInfoUtil->getOS();
        $ip_address = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowser();
        $location = $this->visitorInfoUtil->getLocation($ip_address);

        // escape inputs
        $ip_address = $this->securityUtil->escapeString($ip_address);
        $browser = $this->securityUtil->escapeString($browser);

        // check if visitor found in database
        if ($this->visitorManager->getVisitorRepository($ip_address) == null) {

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

    /**
     * Inserts a new visitor record into the database.
     *
     * @param string $date The date of the visit.
     * @param string $ip_address The IP address of the visitor.
     * @param string $browser The browser used by the visitor.
     * @param string $os The operating system of the visitor.
     * @param array<string, string> $location The location information of the visitor, including 'city' and 'country'.
     *
     * @throws \Exception If an error occurs during the database flush.
     */
    public function insertNewVisitor(string $date, string $ip_address, string $browser, string $os, array $location): void 
    {
        // log geolocate error
        if ($location == 'Unknown') {
            $this->logManager->log('geolocate-error', 'error to geolocate ip: '.$ip_address);
        }

        // prevent maximal useragent to save
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
        $visitorEntity->setStatus('online');
        $visitorEntity->setStatusUpdateTime(strval(time()));
            
        // insert new visitor
        try {
            $this->entityManager->persist($visitorEntity);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
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
            $this->errorManager->handleError('unexpected visitor with ip: '.$ip_address.' update error, please check database structure', 500);
        } else {

            // update values
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
