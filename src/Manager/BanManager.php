<?php

namespace App\Manager;

use App\Entity\Visitor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * BanManager provides methods for banning and unbanning visitors.
 */
class BanManager
{
    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /** * @var VisitorManager */
    private VisitorManager $visitorManager;

    /** * @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;
    
    /**
     * BanManager constructor.
     *
     * @param LogManager             $logManager
     * @param AuthManager            $authManager
     * @param ErrorManager           $errorManager
     * @param VisitorManager         $visitorManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        ErrorManager $errorManager,
        VisitorManager $visitorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Bans a visitor by setting the banned status and reason.
     *
     * @param string $ip_address The IP address of the visitor to ban.
     * @param string $reason The reason for banning the visitor.
     *
     * @throws \Exception If there is an error during the update of the ban status or in case the visitor is not found.
     *
     * @return void
     */
    public function banVisitor(string $ip_address, string $reason): void 
    {
        // get current date
        $date = date('d.m.Y H:i:s');

        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban data
            $visitor->setBannedStatus('yes');
            $visitor->setBanReason($reason);
            $visitor->setBannedTime($date);
            
            // log ban action to database
            $this->logManager->log('ban-system', 'visitor with ip: '.$ip_address.' banned for reason: '.$reason.' by '.$this->authManager->getUsername());

            // update entity data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }

            // close banned visitor messages
            $this->closeAllVisitorMessages($ip_address);

        } else {
            $this->errorManager->handleError('error to ban visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }

    /**
     * Unbans a visitor by updating the banned status.
     *
     * @param string $ip_address The IP address of the visitor to unban.
     *
     * @throws \Exception If there is an error during the update of the ban status or in case the visitor is not found.
     *
     * @return void
     */
    public function unbanVisitor(string $ip_address): void 
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor != null) {

            // update ban status
            $visitor->setBannedStatus('no');
            
            // log ban action to database
            $this->logManager->log('ban-system', 'visitor with ip: '.$ip_address.' unbanned by '.$this->authManager->getUsername());

            // update visitor data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update ban status of visitor-ip: '.$ip_address.', message: '.$e->getMessage(), 500);
            }
        } else {
            $this->errorManager->handleError('error to update ban status of visitor with ip: '.$ip_address.', visitor not found in table', 400);
        }
    }

    /**
     * Checks if a visitor is banned.
     *
     * @param string $ip_address The IP address of the visitor.
     *
     * @return bool True if the visitor is banned, false otherwise.
     */
    public function isVisitorBanned(string $ip_address): bool 
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);
        
        // check if visitor found
        if ($visitor === null) {
            return false;
        } else {

            // check if visitor banned
            if ($visitor->getBannedStatus() == 'yes') {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Retrieves the count of banned visitors.
     *
    * @throws \Exception If there is an error during the database query.
     * 
     * @return int|null The count of banned visitors or null if an error occurs.
     */
    public function getBannedCount(): ?int
    {
        $result = null;
        $repository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            $result = $repository->findBy(['banned_status' => 'yes']);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        return count($result);
    }

    /**
     * Retrieves the ban reason for a visitor.
     *
     * @param string $ip_address The IP address of the visitor.
     *
     * @return string|null The ban reason or null if not found.
     */
    public function getBanReason(string $ip_address): ?string 
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ip_address);

        // check if visitor found
        if ($visitor == null) {
            return null;
        } else {

            // return ban reason string
            return $visitor->getBanReason();
        }
    }

    /**
     * Closes all messages associated with a specific visitor based on their IP address.
     *
     * @param string $ip_address  The IP address of the visitor whose messages should be closed.
     *
     * @throws \Exception If there is an error during the execution of the query.
     *
     * @return void
     */
    public function closeAllVisitorMessages(string $ip_address)
    {
        // sql query builder
        $query = $this->entityManager->createQuery(
            'UPDATE App\Entity\Message m
             SET m.status = :status
             WHERE m.ip_address = :ip_address'
        );
    
        // set closed message
        $query->setParameter('status', 'closed');
        $query->setParameter('ip_address', $ip_address);
    
        // execute query
        try {
            $query->execute();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to close all visitor messages: '.$e->getMessage(), 500);
        }
    }

    /**
     * Retrieves the IP address of a visitor by ID.
     *
     * @param int $id The ID of the visitor.
     *
     * @return string The IP address of the visitor.
     */
    public function getVisitorIP(int $id): string
    {
        $repo = $this->visitorManager->getVisitorRepositoryByID($id);
        return $repo->getIpAddress();
    }
}
