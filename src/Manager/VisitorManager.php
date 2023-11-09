<?php

namespace App\Manager;

use App\Entity\Visitor;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitor manager provides methods for manage visitors
*/

class VisitorManager
{
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil; 
    private EntityManagerInterface $entityManager;

    public function __construct(
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    public function updateVisitorsStatus(): void
    {
        // timeout (seconds)
        $session_timeout_seconds = 180;

        // get current timestamp
        $current_time = time();
        
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);
            
        // check if visitor found
        if ($visitorRepository !== null) {
                
            // get visitors list
            $visitors = $visitorRepository->findAll();

            // update all offline statuses
            foreach ($visitors as $visitor) {

                // get timestamp
                $last_activity_timestamp = $visitor->getStatusUpdateTime();

                // update only online visitors
                if ($visitor->getStatus() === 'online') {
                    if ($current_time - intval($last_activity_timestamp) >= $session_timeout_seconds) {
                        $visitor->setStatus('offline');
                    }
                }
            }
        
            // update visitor status
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update visitor status: '.$e->getMessage(), 500);
            }
        }
    }

    public function getRepositoryByArray(array $search): ?object
    {
        $result = null;
        
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            $result = $visitorRepository->findOneBy($search);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        // return result
        if ($result !== null) {
            return $result;
        } else {
            return null;
        }
    }

    public function getVisitorID(string $ip_address): ?int 
    {
        // try to get visitor data
        $result = $this->getVisitorRepository($ip_address);

        if ($result === null) {
            return 0;
        } else {
            return $result->getID();
        }
    }

    public function updateVisitorEmail(string $ip_address, string $email): void
    {
        $visitor = $this->getVisitorRepository($ip_address);

        // check visitor found
        if ($visitor !== null) {
            $visitor->setEmail($email);

            // try to update email
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
            }           
        }
    }

    public function getVisitors(int $page): ?array
    {
        $repo = $this->entityManager->getRepository(Visitor::class);
        $per_page = $_ENV['ITEMS_PER_PAGE'];
        
        // calculate offset
        $offset = ($page - 1) * $per_page;
    
        // get visitors from database
        try {
            $queryBuilder = $repo->createQueryBuilder('l')
                ->setFirstResult($offset)  
                ->setMaxResults($per_page);
    
            $visitors = $queryBuilder->getQuery()->getResult();

        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get visitors: ' . $e->getMessage(), 500);
            $visitors = [];
        }
    
        // replace browser with formated value for log reader
        foreach ($visitors as $visitor) {
            $user_agent = $visitor->getBrowser();   
            $formated_browser = $this->visitorInfoUtil->getBrowserShortify($user_agent);
            $visitor->setBrowser($formated_browser);
        }

        return $visitors;
    }

    public function getVisitorLanguage(): ?string
    {
        $repo = $this->getVisitorRepository($this->visitorInfoUtil->getIP());
     
        // check visitor found
        if ($repo !== null) {
            return strtolower($repo->getCountry());   
        } else {
            return null;
        }
    }

    public function getVisitorRepositoryByID(int $id): ?object 
    {
        return $this->getRepositoryByArray(['id' => $id]);
    }

    public function getVisitorRepository(string $ip_address): ?object 
    {
        return $this->getRepositoryByArray(['ip_address' => $ip_address]);
    }
    
    public function getVisitorsCount(int $page): int
    {
        return count($this->getVisitors($page));
    }

    public function getVisitorsWhereStstus(string $status): ?array
    {
        return $this->entityManager->getRepository(Visitor::class)->findBy(['status' => $status]);
    }
}
