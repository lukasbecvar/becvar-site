<?php

namespace App\Manager;

use App\Entity\Visitor;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
 *
 * BanManager provides methods for banning and unbanning visitors
 *
 * @package App\Manager
 */
class BanManager
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private VisitorManager $visitorManager;
    private EntityManagerInterface $entityManager;

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
     * Bans a visitor by setting the banned status and reason
     *
     * @param string $ipAddress The IP address of the visitor to ban
     * @param string $reason The reason for banning the visitor
     *
     * @throws \App\Exception\AppErrorException Error to ban visitor
     *
     * @return void
     */
    public function banVisitor(string $ipAddress, string $reason): void
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor != null) {
            // update ban data
            $visitor->setBannedStatus(true)
                ->setBanReason($reason)
                ->setBannedTime(new DateTime());

            // log ban action
            $this->logManager->log(
                name: 'ban-system',
                value:'visitor with ip: ' . $ipAddress . ' banned for reason: ' . $reason . ' by ' . $this->authManager->getUsername()
            );

            try {
                // update entity data
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError(
                    'error to update ban status of visitor-ip: ' . $ipAddress . ', message: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            // close banned visitor messages
            $this->closeAllVisitorMessages($ipAddress);
        } else {
            $this->errorManager->handleError(
                'error to ban visitor with ip: ' . $ipAddress . ', visitor not found in table',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Unbans a visitor by updating the banned status
     *
     * @param string $ipAddress The IP address of the visitor to unban
     *
     * @throws \App\Exception\AppErrorException Error to unban visitor
     *
     * @return void
     */
    public function unbanVisitor(string $ipAddress): void
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor != null) {
            // update ban status
            $visitor->setBannedStatus(false);

            // log ban action
            $this->logManager->log(
                name: 'ban-system',
                value: 'visitor with ip: ' . $ipAddress . ' unbanned by ' . $this->authManager->getUsername()
            );

            try {
                // update visitor data
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError(
                    'error to update ban status of visitor-ip: ' . $ipAddress . ', message: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        } else {
            $this->errorManager->handleError(
                'error to update ban status of visitor with ip: ' . $ipAddress . ', visitor not found in table',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Checks if a visitor is banned
     *
     * @param string $ipAddress The IP address of the visitor
     *
     * @return bool True if the visitor is banned, false otherwise
     */
    public function isVisitorBanned(string $ipAddress): bool
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor != null) {
            // check if visitor banned
            if ($visitor->getBannedStatus() == 'yes') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the count of banned visitors
     *
    * @throws \App\Exception\AppErrorException Error get banned count
     *
     * @return int|null The count of banned visitors or null if an error occurs
     */
    public function getBannedCount(): ?int
    {
        $repository = $this->entityManager->getRepository(Visitor::class);

        try {
            // count banned users
            return $repository->count(['banned_status' => 'yes']);
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'find error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get the ban reason for a visitor
     *
     * @param string $ipAddress The IP address of the visitor
     *
     * @return string|null The ban reason or null if not found
     */
    public function getBanReason(string $ipAddress): ?string
    {
        // get visitor data
        $visitor = $this->visitorManager->getVisitorRepository($ipAddress);

        // check if visitor found
        if ($visitor != null) {
            // return ban reason string
            return $visitor->getBanReason();
        }

        return null;
    }

    /**
     * Closes all messages associated with a specific visitor based on their IP address
     *
     * @param string $ipAddress  The IP address of the visitor whose messages should be closed
     *
     * @throws \App\Exception\AppErrorException Error close all visitor messages
     *
     * @return void
     */
    public function closeAllVisitorMessages(string $ipAddress)
    {
        // sql query builder
        $query = $this->entityManager->createQuery(
            'UPDATE App\Entity\Message m
             SET m.status = :status
             WHERE m.ip_address = :ip_address'
        );

        try {
            // set closed message
            $query->setParameter('status', 'closed');
            $query->setParameter('ip_address', $ipAddress);

            // execute query
            $query->execute();
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to close all visitor messages: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get the IP address of a visitor by ID
     *
     * @param int $id The ID of the visitor
     *
     * @return string The IP address of the visitor
     */
    public function getVisitorIP(int $id): string
    {
        return $this->visitorManager->getVisitorRepositoryByID($id)->getIpAddress();
    }
}
