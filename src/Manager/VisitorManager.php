<?php

namespace App\Manager;

use App\Entity\Visitor;
use App\Util\CacheUtil;
use App\Util\VisitorInfoUtil;
use App\Repository\VisitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
 *
 * Visitor manager provides methods for managing visitors
 *
 * @package App\Manager
 */
class VisitorManager
{
    private CacheUtil $cacheUtil;
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private VisitorRepository $visitorRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CacheUtil $cacheUtil,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        VisitorRepository $visitorRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->cacheUtil = $cacheUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->visitorRepository = $visitorRepository;
    }

    /**
     * Get a visitor repository by array search criteria
     *
     * @param array<string,mixed> $search The search criteria
     *
     * @throws \App\Exception\AppErrorException Error get visitor
     *
     * @return Visitor|null The visitor entity if found, null otherwise
     */
    public function getRepositoryByArray(array $search): ?object
    {
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            return $visitorRepository->findOneBy($search);
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'find error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return null;
        }
    }

    /**
     * Get the visitor ID by IP address
     *
     * @param string $ipAddress The IP address of the visitor
     *
     * @return int The ID of the visitor
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
     * Update visitor email by IP address
     *
     * @param string $ipAddress The IP address of the visitor
     * @param string $email The email address of the visitor
     *
     * @throws \App\Exception\AppErrorException Error to update visitor email
     *
     * @return void
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
                $this->errorManager->handleError(
                    'flush error: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    /**
     * Get a paginated list of visitors
     *
     * @param int $page The page number
     * @param string $filter The filter value
     *
     * @return Visitor[]|null The list of visitors if found, null otherwise
     */
    public function getVisitors(int $page, string $filter = '1'): ?array
    {
        $repo = $this->entityManager->getRepository(Visitor::class);
        $perPage = $_ENV['ITEMS_PER_PAGE'];

        // calculate offset
        $offset = ($page - 1) * $perPage;

        // get visitors from database
        try {
            if ($filter == '1') {
                $queryBuilder = $repo->createQueryBuilder('l')
                    ->setFirstResult($offset)
                    ->setMaxResults($perPage);
            } else {
                $queryBuilder = $repo->createQueryBuilder('l');
            }

            $visitors = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get visitors: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $visitors = [];
        }

        // replace browser with formated value for log reader
        foreach ($visitors as $key => $visitor) {
            if ($filter == 'online') {
                if (!in_array($visitor->getId(), $this->getOnlineVisitorIDs())) {
                    unset($visitors[$key]);
                }
            }

            $userAgent = $visitor->getBrowser();
            $formatedBrowser = $this->visitorInfoUtil->getBrowserShortify($userAgent);
            $visitor->setBrowser($formatedBrowser);
        }

        return $visitors;
    }

    /**
     * Get the visitor language based on IP address
     *
     * @return string|null The language of the visitor
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
     * Get a visitor repository by ID
     *
     * @param int $id The ID of the visitor
     *
     * @return Visitor|null The visitor entity if found, null otherwise
     */
    public function getVisitorRepositoryByID(int $id): ?object
    {
        return $this->getRepositoryByArray(['id' => $id]);
    }

    /**
     * Get a visitor repository by IP address
     *
     * @param string $ipAddress The IP address of the visitor
     *
     * @return Visitor|null The visitor entity if found, null otherwise
     */
    public function getVisitorRepository(string $ipAddress): ?object
    {
        return $this->getRepositoryByArray(['ip_address' => $ipAddress]);
    }

    /**
     * Get the count of visitors for a given page
     *
     * @param int $page The page number
     *
     * @return int The count of visitors for the given page
     */
    public function getVisitorsCount(int $page): int
    {
        return count($this->getVisitors($page));
    }

    /**
     * Get the status of a visitor with the given ID
     *
     * @param int $id The ID of the visitor.
     * @return string The status of the visitor ('online' if online, 'offline' if not found or offline)
     */
    public function getVisitorStatus(int $id): string
    {
        $userCacheKey = 'online_user_' . $id;

        // get user status
        $status = $this->cacheUtil->getValue($userCacheKey);

        // check if status found
        if ($status->get() == null) {
            return 'offline';
        }

        return $status->get();
    }

    /**
     * Get an array of IDs of online visitors
     *
     * @return array<int> An array containing IDs of visitors who are currently online
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
