<?php

namespace App\Manager;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

/*
    Messages manager provides methods for manager inbox/contact system
*/

class MessagesManager
{
    private $errorManager;
    private $entityManager;

    public function __construct(ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
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

    public function getMessages(string $status, int $page)
    {
        $repository = $this->entityManager->getRepository(Message::class);
        $limit = $_ENV['ITEMS_PER_PAGE'];
        
        // calculate offset
        $offset = ($page - 1) * $limit;
    
        // get messages entity from database
        try {
            return $repository->findBy(['status' => $status], null, $limit, $offset);
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
