<?php

namespace App\Middleware;

use Twig\Environment;
use App\Util\CacheUtil;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Manager\BanManager;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ErrorManager;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

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
    private CacheUtil $cacheUtil;
    private BanManager $banManager;
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Environment $twig,
        CacheUtil $cacheUtil,
        LogManager $logManager,
        BanManager $banManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->twig = $twig;
        $this->cacheUtil = $cacheUtil;
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
        // get data to insert
        $date = date('d.m.Y H:i');
        $os = $this->visitorInfoUtil->getOS();
        $ipAddress = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowser();

        // escape inputs
        $ipAddress = $this->securityUtil->escapeString($ipAddress);
        $browser = $this->securityUtil->escapeString($browser);

        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found in database
        if ($visitor == null) {
            // save new visitor data
            $this->insertNewVisitor($date, $ipAddress, $browser, $os);
        } else {
            // cache online visitor
            $this->cacheUtil->setValue('online_user_' . $visitor->getId(), 'online', 300);

            // check if visitor banned
            if ($this->banManager->isVisitorBanned($ipAddress)) {
                $reason = $this->banManager->getBanReason($ipAddress);
                $this->logManager->log(
                    name: 'ban-system',
                    value: 'visitor with ip: ' . $ipAddress . ' trying to access page, but visitor banned for: ' . $reason
                );

                // render banned page
                die($this->twig->render('errors/error-banned.twig', [
                    'message' => $reason,
                    'contactEmail' => $_ENV['CONTACT_EMAIL']
                ]));
            } else {
                // update exist visitor
                $this->updateVisitor($date, $ipAddress, $browser, $os);
            }
        }
    }

    /**
     * Inserts a new visitor record into the database.
     *
     * @param string $date The date of the visit.
     * @param string $ipAddress The IP address of the visitor.
     * @param string $browser The browser used by the visitor.
     * @param string $os The operating system of the visitor.
     *
     * @throws \App\Exception\AppErrorException If an error occurs during the database flush.
     *
     * @return void
     */
    public function insertNewVisitor(string $date, string $ipAddress, string $browser, string $os): void
    {
        // get visitor ip address
        $location = $this->visitorInfoUtil->getLocation($ipAddress);

        // log geolocate error
        if ($location['city'] == 'Unknown' || $location['country'] == 'Unknown') {
            $this->logManager->log('geolocate-error', 'error to geolocate ip: ' . $ipAddress);
        }

        // prevent maximal user agent length
        if (strlen($browser) >= 200) {
            $browser = substr($browser, 0, 197) . "...";
        }

        // create new visitor entity
        $visitorEntity = new Visitor();

        // set visitor data
        $visitorEntity->setFirstVisit($date)
            ->setLastVisit($date)
            ->setBrowser($browser)
            ->setOs($os)
            ->setCity($location['city'])
            ->setCountry($location['country'])
            ->setIpAddress($ipAddress)
            ->setBannedStatus('no')
            ->setBanReason('non-banned')
            ->setBannedTime(('non-banned'))
            ->setEmail('unknown');

        // try to insert new visitor
        try {
            $this->entityManager->persist($visitorEntity);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'flush error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Updates an existing visitor record in the database.
     *
     * @param string $date The date of the visit.
     * @param string $ipAddress The IP address of the visitor.
     * @param string $browser The updated browser used by the visitor.
     * @param string $os The updated operating system of the visitor.
     *
     * @throws \App\Exception\AppErrorException If an error occurs during the database flush.
     *
     * @return void
     */
    public function updateVisitor(string $date, string $ipAddress, string $browser, string $os): void
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // prevent maximal useragent to save
        if (strlen($browser) >= 200) {
            $browser = substr($browser, 0, 197) . "...";
        }

        // check if visitor data found
        if (!$visitor != null) {
            $this->errorManager->handleError(
                'unexpected visitor with ip: ' . $ipAddress . ' update error, please check database structure',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } else {
            // update values
            $visitor->setLastVisit($date);
            $visitor->setBrowser($browser);
            $visitor->setOs($os);

            // try to update data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError(
                    'flush error: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
