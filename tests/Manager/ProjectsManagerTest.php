<?php

namespace App\Tests\Manager;

use App\Util\JsonUtil;
use App\Entity\Project;
use App\Util\CacheUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use App\Manager\ProjectsManager;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProjectsManagerTest
 *
 * Test cases for project manager component
 *
 * @package App\Tests\Manager
 */
class ProjectsManagerTest extends TestCase
{
    private ProjectsManager $projectsManager;
    private JsonUtil & MockObject $jsonUtil;
    private CacheUtil & MockObject $cacheUtil;
    private LogManager & MockObject $logManager;
    private ErrorManager & MockObject $errorManager;
    private SecurityUtil & MockObject $securityUtil;
    private ProjectRepository & MockObject $projectRepository;
    private EntityManagerInterface & MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->jsonUtil = $this->createMock(JsonUtil::class);
        $this->cacheUtil = $this->createMock(CacheUtil::class);
        $this->logManager = $this->createMock(LogManager::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->securityUtil = $this->createMock(SecurityUtil::class);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // create project manager instance
        $this->projectsManager = new ProjectsManager(
            $this->jsonUtil,
            $this->cacheUtil,
            $this->logManager,
            $this->errorManager,
            $this->securityUtil,
            $this->projectRepository,
            $this->entityManager
        );
    }

    /**
     * Test drop all projects from the database
     *
     * @return void
     */
    public function testDropProjects(): void
    {
        // mock project repository
        $project = $this->createMock(Project::class);
        $this->projectRepository->method('findAll')->willReturn([$project]);

        // expect remove projects
        $this->entityManager->expects($this->once())->method('remove')
            ->with($this->isInstanceOf(Project::class));
        $this->entityManager->expects($this->once())->method('flush');

        // call tested method
        $this->projectsManager->dropProjects();
    }

    /**
     * Test reset AUTO_INCREMENT value for the projects table
     *
     * @return void
     */
    public function testResetIndex(): void
    {
        // mock get table name
        $classMetadataMock = $this->createMock(ClassMetadata::class);
        $classMetadataMock->method('getTableName')->willReturn('projects');
        $this->entityManager->method('getClassMetadata')
            ->willReturn($classMetadataMock);

        // mock database connection
        $connectionMock = $this->createMock(Connection::class);
        $this->entityManager->method('getConnection')->willReturn($connectionMock);

        // expect reset auto increment
        $connectionMock->expects($this->once())->method('executeQuery')
            ->with($this->stringContains('ALTER TABLE projects AUTO_INCREMENT = 0'));

        // call tested method
        $this->projectsManager->resetIndex();
    }


    /**
     * Test get list of projects based on their status
     *
     * @return void
     */
    public function testGetProjectsList(): void
    {
        $status = 'open';
        $projectsList = [new Project(), new Project()];

        // mock cache and repository behavior
        $this->cacheUtil->method('isCatched')->willReturn(false);
        $this->projectRepository->method('getProjectsByStatus')->willReturn($projectsList);

        // mock cache saving
        $this->cacheUtil->expects($this->once())->method('setValue')
            ->with('projects-list-' . $status, $projectsList, 60 * 60 * 24 * 30);

        // call tested method
        $result = $this->projectsManager->getProjectsList($status);

        // assert result
        $this->assertCount(2, $result);
    }

    /**
     * Test get total count of projects
     *
     * @return void
     */
    public function testGetProjectsCount(): void
    {
        $projectsCount = 10;

        // mock cache and repository behavior
        $this->cacheUtil->method('isCatched')->willReturn(false);
        $this->projectRepository->method('count')->willReturn($projectsCount);

        // mock cache saving
        $this->cacheUtil->expects($this->once())->method('setValue')
            ->with('projects-count', $projectsCount, 60 * 60 * 24 * 30);

        // call tested method
        $result = $this->projectsManager->getProjectsCount();

        // assert result
        $this->assertEquals(10, $result);
    }
}
