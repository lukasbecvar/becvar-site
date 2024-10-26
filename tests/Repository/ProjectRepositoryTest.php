<?php

namespace App\Tests\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ProjectRepositoryTest
 *
 * Test for doctrine entity repository class
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
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

        // assert item result
        foreach ($projects as $project) {
            $this->assertInstanceOf(Project::class, $project, 'Each item should be an instance of Project');
            $this->assertEquals($status, $project->getStatus(), 'The project status should match the filter');
        }
    }
}
