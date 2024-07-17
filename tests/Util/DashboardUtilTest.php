<?php

namespace App\Tests\Util;

use App\Util\JsonUtil;
use App\Util\DashboardUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class DashboardUtilTest
 *
 * Test cases for DashboardUtil class
 *
 * @package App\Tests\Util
 */
class DashboardUtilTest extends TestCase
{
    private JsonUtil|MockObject $jsonUtil;
    private ErrorManager|MockObject $errorManager;
    private DashboardUtil|MockObject $dashboardUtil;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        $this->jsonUtil = $this->createMock(JsonUtil::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->dashboardUtil = new DashboardUtil($this->jsonUtil, $this->errorManager, $this->entityManager);
    }

    /**
     * Test get database entity count
     *
     * @return void
     */
    public function testGetDatabaseEntityCount(): void
    {
        $entity = new class {
        };
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn(['entity1', 'entity2', 'entity3']);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(get_class($entity))
            ->willReturn($repository);

        $count = $this->dashboardUtil->getDatabaseEntityCount($entity);
        $this->assertEquals(3, $count);
    }

    /**
     * Test get database entity count with search criteria
     *
     * @return void
     */
    public function testGetDatabaseEntityCountWithSearchCriteria(): void
    {
        $entity = new class {
        };
        $searchCriteria = ['field' => 'value'];
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with($searchCriteria)
            ->willReturn(['entity1', 'entity2']);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(get_class($entity))
            ->willReturn($repository);

        $count = $this->dashboardUtil->getDatabaseEntityCount($entity, $searchCriteria);
        $this->assertEquals(2, $count);
    }

    /**
     * Test is browser list found
     *
     * @return void
     */
    public function testIsBrowserListFound(): void
    {
        $this->jsonUtil->expects($this->once())
            ->method('getJson')
            ->with($this->stringContains('/../../config/browser-list.json'))
            ->willReturn(['some', 'data']);

        $result = $this->dashboardUtil->isBrowserListFound();
        $this->assertTrue($result);
    }

    /**
     * Test is browser list not found
     *
     * @return void
     */
    public function testIsBrowserListNotFound(): void
    {
        $this->jsonUtil->expects($this->once())
            ->method('getJson')
            ->with($this->stringContains('/../../config/browser-list.json'))
            ->willReturn(null);

        $result = $this->dashboardUtil->isBrowserListFound();
        $this->assertFalse($result);
    }
}
