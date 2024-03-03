<?php

namespace App\Service\Manager;

use App\Entity\Message;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/** 
 * Class AuthManager
 * 
 * Messages manager provides methods for managing inbox/contact system
 * 
 * @package App\Service\Manager
*/
class MessagesManager
{
    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var EntityManagerInterface
     * Instance of the EntityManagerInterface for interacting with the database.
     */
    private EntityManagerInterface $entityManager;

    /**
     * MessagesManager constructor.
     *
     * @param SecurityUtil           $securityUtil
     * @param ErrorManager           $errorManager
     * @param VisitorManager         $visitorManager
     * @param EntityManagerInterface $entityManager
     */
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
     * Saves a new message to the database.
     *
     * @param string $name
     * @param string $email
     * @param string $message_input
     * @param string $ip_address
     * @param string $visitor_id
     *
     * @return bool
     */
    public function saveMessage(string $name, string $email, string $message_input, string $ip_address, string $visitor_id): bool
    {
        $message = new Message();

        // get data & time
        $date = date('d.m.Y H:i:s');

        // update visitor email
        $this->visitorManager->updateVisitorEmail($ip_address, $email);

        // ecrypt message
        $message_input = $this->securityUtil->encryptAes($message_input);
        
        // set message entity values
        $message->setName($name);
        $message->setEmail($email);
        $message->setMessage($message_input);
        $message->setTime($date);
        $message->setIpAddress($ip_address);
        $message->setStatus('open');
        $message->setVisitorID($visitor_id);
        
        // insert new message
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();
                                    
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Gets the count of open messages from a specific IP address.
     *
     * @param string $ip_address
     *
     * @return int
     */
    public function getMessageCountByIpAddress(string $ip_address): int
    {
        // build query
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(m.id) FROM App\Entity\Message m WHERE m.ip_address = :ip_address AND m.status = :status'
        );

        // set query parameter
        $query->setParameter('status', 'open');
        $query->setParameter('ip_address', $ip_address);
    
        // execute query
        try {
            return $query->getSingleScalarResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get messages count: '.$e->getMessage(), 500);
            return 0;
        }
    }

    /**
     * Gets messages based on status and pagination.
     *
     * @param string $status
     * @param int $page
     *
     * @return array<array<int|string>>|null An array of messages if successful, or null if an error occurs.
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

            foreach ($inbox as $inbox_message) {

                // decrypt message
                $message_decrypted = $this->securityUtil->decryptAes($inbox_message->getMessage());

                // check if message data is decrypted
                if ($message_decrypted == null) {
                    $this->errorManager->handleError('Error to decrypt aes message data', 500);
                }

                // build message content
                $message = [
                    'id' => $inbox_message->getId(),
                    'name' => $inbox_message->getName(),
                    'email' => $inbox_message->getEmail(),
                    'message' => $message_decrypted,
                    'time' => $inbox_message->getTime(),
                    'ip_address' => $inbox_message->getIpAddress(),
                    'status' => $inbox_message->getStatus(),
                    'visitor_id' => $inbox_message->getVisitorId()
                ];  

                // add message to final list
                array_push($messages, $message);
            }
            
            return $messages;
        
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get messages: ' . $e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Closes a message by updating its status to 'closed'.
     *
     * @param int $id
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
                
                // update in database
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to close message: '.$id.', '.$e->getMessage(), 500);
            }
        }
    }
}
