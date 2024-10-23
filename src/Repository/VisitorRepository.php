<?php

namespace App\Repository;

use DateTime;
use DateInterval;
use App\Entity\Visitor;
use InvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Visitor>
 *
 * @method Visitor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visitor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visitor[]    findAll()
 * @method Visitor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visitor::class);
    }

    /**
     * Retrieves a list of all IDs from the database
     *
     * @return array<int> An array containing all IDs from the database
     */
    public function getAllIds(): array
    {
        // select ids
        $queryBuilder = $this->createQueryBuilder('v')->select('v.id');
        $query = $queryBuilder->getQuery();

        // get results
        $results = $query->getScalarResult();

        // return id list
        return array_column($results, 'id');
    }

    /**
     * Finds visitors based on the specified time filter
     *
     * @param string $filter The filter for the time period
     *
     * @return array<mixed> An array of visitors filtered by the specified time range
     *
     * @throws InvalidArgumentException If the filter is not valid
     */
    public function findByTimeFilter(string $filter): array
    {
        $now = new DateTime();
        $startDate = null;

        // calculate start date based on the filter
        switch ($filter) {
            case 'H':
                $startDate = $now->sub(new DateInterval('PT1H'));
                break;
            case 'D':
                $startDate = $now->sub(new DateInterval('P1D'));
                break;
            case 'W':
                $startDate = $now->sub(new DateInterval('P7D'));
                break;
            case 'M':
                $startDate = $now->sub(new DateInterval('P1M'));
                break;
            case 'Y':
                $startDate = $now->sub(new DateInterval('P1Y'));
                break;
            case 'ALL':
                return $this->findAll();
            default:
                throw new InvalidArgumentException("Invalid filter: $filter");
        }

        // create a query builder
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.first_visit >= :start_date')->setParameter('start_date', $startDate);

        return $qb->getQuery()->getResult();
    }
}
