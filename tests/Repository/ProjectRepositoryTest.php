<?php

namespace App\Tests\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ProjectRepositoryTest
 *
 * Test cases for doctrine project repository
 *
 * @package App\Tests\Repository
 */
class ProjectRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Test get projects by status
     *
     * @return void
     */
    public function testGetProjectsByStatus(): void
    {
        /** @var \App\Repository\ProjectRepository $projectRepository */
        $projectRepository = $this->entityManager->getRepository(Project::class);

        $status = 'open';
        $projects = $projectRepository->getProjectsByStatus($status);

        // assert result
        $this->assertIsArray($projects, 'Projects should be returned as an array');
        $this->assertNotEmpty($projects, 'Projects should not be empty');
    }
}
