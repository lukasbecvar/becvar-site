<?php

namespace App\Manager;

use App\Entity\Visitor;
use App\Util\VisitorInfoUtil;
use App\Repository\VisitorRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthManager
 * 
 * Visitor manager provides methods for managing visitors.
 * 
 * @package App\Manager
 */
class VisitorManager
{
    private CacheManager $cacheManager;
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private VisitorRepository $visitorRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CacheManager $cacheManager,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        VisitorRepository $visitorRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->cacheManager = $cacheManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->visitorRepository = $visitorRepository;
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
        }

        return $visitor->getID();
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
        } 
        
        return null;
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
     * Retrieves the status of a visitor with the given ID.
     *
     * This method constructs a cache key for the specified visitor ID, retrieves the status from the cache manager,
     * and returns the status if found. If the status is not found in the cache or if it's offline, 'offline' is returned.
     *
     * @param int $id The ID of the visitor.
     * @return string The status of the visitor ('online' if online, 'offline' if not found or offline).
     */
    public function getVisitorStatus(int $id): string 
    {
        $user_cache_key = 'online_user_'.$id;

        // get user status
        $status = $this->cacheManager->getValue($user_cache_key);

        // check if status found
        if ($status->get() == null) {
            return 'offline';
        }

        return $status->get();
    }

    /**
     * Retrieves an array of IDs of online visitors.
     *
     * This method retrieves a list of all visitor IDs from the visitor repository,
     * checks the status of each visitor using the getVisitorStatus() method,
     * and returns an array containing IDs of visitors who are currently online.
     *
     * @return array<int> An array containing IDs of visitors who are currently online.
     */
    public function getOnlineVisitorIDs(): array
    {
        $online_visitors = [];

        // get all visitors id list
        $visitor_ids = $this->visitorRepository->getAllIds();

        foreach ($visitor_ids as $visitor_id) {

            // get visitor status
            $status = $this->getVisitorStatus($visitor_id);

            // check visitor status
            if ($status == 'online') {
                array_push($online_visitors, $visitor_id);
            }
        }

        return $online_visitors;
    }
}
