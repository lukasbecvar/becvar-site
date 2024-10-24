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
     * Get user by token
     *
     * @param string $token The user token
     *
     * @return User|null The user entity if found
     */
    public function getUserByToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retrieves a list of all users along with their associated visitor IDs
     *
     * @return array<array<string>> User list with associated visitor IDs
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
