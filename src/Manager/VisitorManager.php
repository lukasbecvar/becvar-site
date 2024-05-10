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
            $this->errorManager->handleError('find error: ' . $e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Get the visitor ID by IP address.
     *
     * @param string $ipAddress
     *
     * @return int
     */
    public function getVisitorID(string $ipAddress): int
    {
        // get visitor id
        $visitor = $this->getVisitorRepository($ipAddress);

        if ($visitor == null) {
            return 1;
        }

        return $visitor->getID();
    }

    /**
     * Update visitor email by IP address.
     *
     * @param string $ipAddress
     * @param string $email
     */
    public function updateVisitorEmail(string $ipAddress, string $email): void
    {
        $visitor = $this->getVisitorRepository($ipAddress);

        // check visitor found
        if ($visitor !== null) {
            $visitor->setEmail($email);

            // try to update email
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: ' . $e->getMessage(), 500);
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
        $perPage = $_ENV['ITEMS_PER_PAGE'];

        // calculate offset
        $offset = ($page - 1) * $perPage;

        // get visitors from database
        try {
            $queryBuilder = $repo->createQueryBuilder('l')
                ->setFirstResult($offset)
                ->setMaxResults($perPage);

            $visitors = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get visitors: ' . $e->getMessage(), 500);
            $visitors = [];
        }

        // replace browser with formated value for log reader
        foreach ($visitors as $visitor) {
            $userAgent = $visitor->getBrowser();
            $formatedBrowser = $this->visitorInfoUtil->getBrowserShortify($userAgent);
            $visitor->setBrowser($formatedBrowser);
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
     * @param string $ipAddress
     *
     * @return Visitor|null
     */
    public function getVisitorRepository(string $ipAddress): ?object
    {
        return $this->getRepositoryByArray(['ip_address' => $ipAddress]);
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
        $userCacheKey = 'online_user_' . $id;

        // get user status
        $status = $this->cacheManager->getValue($userCacheKey);

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
        $onlineVisitors = [];

        // get all visitors id list
        $visitorIds = $this->visitorRepository->getAllIds();

        foreach ($visitorIds as $visitorId) {
            // get visitor status
            $status = $this->getVisitorStatus($visitorId);

            // check visitor status
            if ($status == 'online') {
                array_push($onlineVisitors, $visitorId);
            }
        }

        return $onlineVisitors;
    }
}
