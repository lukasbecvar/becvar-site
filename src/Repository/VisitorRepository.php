<?php

namespace App\Repository;

use App\Entity\Visitor;
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
     * @throws \InvalidArgumentException If the filter is not valid
     */
    public function findByTimeFilter(string $filter): array
    {
        // load all records
        $visitors = $this->findBy([]);

        // current time
        $now = new \DateTime();
        $startDate = null;

        // calculate startDate based on the filter
        switch ($filter) {
            case 'H':
                $startDate = $now->sub(new \DateInterval('PT1H'));
                break;
            case 'D':
                $startDate = $now->sub(new \DateInterval('P1D'));
                break;
            case 'W':
                $startDate = $now->sub(new \DateInterval('P7D'));
                break;
            case 'M':
                $startDate = $now->sub(new \DateInterval('P1M'));
                break;
            case 'Y':
                $startDate = $now->sub(new \DateInterval('P1Y'));
                break;
            case 'ALL':
                return $visitors;
            default:
                throw new \InvalidArgumentException("Invalid filter: $filter");
        }

        // filter results in PHP
        return array_filter($visitors, function ($visitor) use ($startDate) {
            // convert first_visit string to DateTime object
            $visitorDate = \DateTime::createFromFormat('d.m.Y H:i', $visitor->getFirstVisit());

            // check if the date is valid and greater than or equal to startDate
            return $visitorDate && $visitorDate >= $startDate;
        });
    }
}
