<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class ProjectRepository
 *
 * Repository for project database entity
 *
 * @extends ServiceEntityRepository<Project>
 *
 * @package App\Repository
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Get project list by status
     *
     * @param string $status The status of the projects
     *
     * @return array<mixed> An array of projects filtered by the specified status
     */
    public function getProjectsByStatus(string $status): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', $status);

        return $queryBuilder->getQuery()->getResult();
    }
}
