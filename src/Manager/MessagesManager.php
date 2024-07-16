<?php

namespace App\Manager;

use App\Entity\Message;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
 *
 * Messages manager provides methods for managing inbox/contact system
 *
 * @package App\Manager
*/
class MessagesManager
{
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private VisitorManager $visitorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        VisitorManager $visitorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Saves a new message to the database
     *
     * @param string $name The name of the sender
     * @param string $email The email address of the sender
     * @param string $messageInput The message input
     * @param string $ipAddress The IP address of the sender
     * @param string $visitorId The ID of the visitor associated with the sender
     * 
     * @throws \App\Exception\AppErrorException Error to save message
     *
     * @return bool True if the message is saved successfully, false otherwise
     */
    public function saveMessage(string $name, string $email, string $messageInput, string $ipAddress, string $visitorId): bool
    {
        $message = new Message();

        // get data & time
        $date = date('d.m.Y H:i:s');

        // update visitor email
        $this->visitorManager->updateVisitorEmail($ipAddress, $email);

        // ecrypt message
        $messageInput = $this->securityUtil->encryptAes($messageInput);

        // set message entity values
        $message->setName($name)
            ->setEmail($email)
            ->setMessage($messageInput)
            ->setTime($date)
            ->setIpAddress($ipAddress)
            ->setStatus('open')
            ->setVisitorID($visitorId);

        // insert new message
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to save message: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return false;
        }
    }

    /**
     * Gets the count of open messages from a specific IP address
     *
     * @param string $ipAddress The IP address of the user
     * 
     * @throws \App\Exception\AppErrorException Error to get messages count
     *
     * @return int The count of open messages from the IP address
     */
    public function getMessageCountByIpAddress(string $ipAddress): int
    {
        // build query
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(m.id) FROM App\Entity\Message m WHERE m.ip_address = :ip_address AND m.status = :status'
        );

        // set query parameter
        $query->setParameter('status', 'open');
        $query->setParameter('ip_address', $ipAddress);

        // execute query
        try {
            return $query->getSingleScalarResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get messages count: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return 0;
        }
    }

    /**
     * Gets messages based on status and pagination
     *
     * @param string $status The status of the messages
     * @param int $page The page number
     * 
     * @throws \App\Exception\AppErrorException Error to get messages
     *
     * @return array<array<int|string>>|null An array of messages if successful, or null if an error occurs
     */
    public function getMessages(string $status, int $page): ?array
    {
        $repository = $this->entityManager->getRepository(Message::class);
        $limit = $_ENV['ITEMS_PER_PAGE'];

        // calculate offset
        $offset = ($page - 1) * $limit;

        // get messages entity from database
        try {
            $inbox = $repository->findBy(['status' => $status], null, $limit, $offset);
            $messages = [];

            foreach ($inbox as $inboxMessage) {
                // decrypt message
                $messageDecrypted = $this->securityUtil->decryptAes($inboxMessage->getMessage());

                // check if message data is decrypted
                if ($messageDecrypted == null) {
                    $this->errorManager->handleError(
                        'Error to decrypt aes message data',
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                // build message content
                $message = [
                    'id' => $inboxMessage->getId(),
                    'name' => $inboxMessage->getName(),
                    'email' => $inboxMessage->getEmail(),
                    'message' => $messageDecrypted,
                    'time' => $inboxMessage->getTime(),
                    'ip_address' => $inboxMessage->getIpAddress(),
                    'status' => $inboxMessage->getStatus(),
                    'visitor_id' => $inboxMessage->getVisitorId()
                ];

                // add message to final list
                array_push($messages, $message);
            }

            return $messages;
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get messages: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return null;
        }
    }

    /**
     * Closes a message by updating its status to 'closed'
     *
     * @param int $id The ID of the message to close
     * 
     * @throws \App\Exception\AppErrorException Error to close message
     *
     * @return void
     */
    public function closeMessage(int $id): void
    {
        $message = $this->entityManager->getRepository(Message::class)->find($id);

        // check if message found
        if ($message !== null) {
            try {
                // close message
                $message->setStatus('closed');
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError(
                    'error to close message: ' . $id . ', ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
