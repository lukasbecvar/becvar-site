<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retrieves a list of all users along with their associated visitor IDs.
     *
     * This method constructs a query to select usernames, roles, and visitor IDs from the entity represented by this repository.
     * It then executes the query and returns an array containing associative arrays for each user,
     * with keys 'username', 'role', and 'visitor_id' representing the respective user details.
     *
     * @return array<array<string>> An array containing details of all users along with their associated visitor IDs.
     *               Each element of the array is an associative array with keys 'username', 'role', and 'visitor_id',
     *               representing the username, role, and associated visitor ID respectively.
     */
    public function getAllUsersWithVisitorId(): array
    {
        // get data
        $queryBuilder = $this->createQueryBuilder('u')->select('u.username, u.role, u.visitor_id');
        $query = $queryBuilder->getQuery();

        // return data array
        return $query->getResult();
    }
}
