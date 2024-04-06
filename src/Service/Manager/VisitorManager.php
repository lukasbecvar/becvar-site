<?php

namespace App\Service\Manager;

use App\Entity\Visitor;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthManager
 * 
 * Visitor manager provides methods for managing visitors.
 * 
 * @package App\Service\Manager
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

    /**
     * Update visitors' online status based on session timeout.
     */
    public function updateVisitorsStatus(): void
    {
        // timeout (seconds)
        $session_timeout_seconds = 60;

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

    /**
     * Get a visitor repository by array search criteria.
     *
     * @param array<string, mixed> $search
     *
     * @return Visitor|null
     */
    public function getRepositoryByArray(array $search): ?object
    {        
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            return $visitorRepository->findOneBy($search);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Get the visitor ID by IP address.
     *
     * @param string $ip_address
     *
     * @return int
     */
    public function getVisitorID(string $ip_address): int 
    {
        // get visitor id
        $visitor = $this->getVisitorRepository($ip_address);

        if ($visitor == null) {
            return 1;
        } else {
            return $visitor->getID();
        }
    }

    /**
     * Update visitor email by IP address.
     *
     * @param string $ip_address
     * @param string $email
     */
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

    /**
     * Get a paginated list of visitors.
     *
     * @param int $page
     *
     * @return Visitor[]|null
     */
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

    /**
     * Get the visitor language based on IP address.
     *
     * @return string|null
     */
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

    /**
     * Get the visitor status by ID.
     *
     * @param int $id
     *
     * @return string|null
     */
    public function getVisitorStatus(int $id): ?string 
    {
        $visitor = $this->getVisitorRepositoryByID($id);

        // check if visitor found
        if ($visitor !== null) {
            return $visitor->getStatus();
        } else {
            return null;
        }
    }

    /**
     * Get a visitor repository by ID.
     *
     * @param int $id
     *
     * @return Visitor|null
     */
    public function getVisitorRepositoryByID(int $id): ?object 
    {
        return $this->getRepositoryByArray(['id' => $id]);
    }

    /**
     * Get a visitor repository by IP address.
     *
     * @param string $ip_address
     *
     * @return Visitor|null
     */
    public function getVisitorRepository(string $ip_address): ?object 
    {
        return $this->getRepositoryByArray(['ip_address' => $ip_address]);
    }
    
    /**
     * Get the count of visitors for a given page.
     *
     * @param int $page
     *
     * @return int
     */
    public function getVisitorsCount(int $page): int
    {
        return count($this->getVisitors($page));
    }

    /**
     * Get visitors by status.
     *
     * @param string $status
     *
     * @return Visitor[]|null
     */
    public function getVisitorsWhereStstus(string $status): ?array
    {
        return $this->entityManager->getRepository(Visitor::class)->findBy(['status' => $status]);
    }
}
