<?php

namespace App\Manager;

use App\Entity\Message;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Messages manager provides methods for manager inbox/contact system
*/

class MessagesManager
{
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SecurityUtil $securityUtil, 
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil, 
        EntityManagerInterface $entityManager
    ) {
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function saveMessage(string $name, string $email, string $message_input, string $ip_address, string $visitor_id): bool
    {
        $message = new Message();

        // get others data
        $date = date('d.m.Y H:i:s');

        // update visitor email
        $this->visitorInfoUtil->updateVisitorEmail($ip_address, $email);

        // ecrypt message
        $message_input = $this->securityUtil->encrypt_aes($message_input);
        
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
                $message_decrypted = $this->securityUtil->decrypt_aes($inbox_message->getMessage());

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
