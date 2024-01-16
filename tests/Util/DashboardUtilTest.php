<?php

namespace App\Tests\Util;

use App\Util\JsonUtil;
use App\Util\DashboardUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DashboardUtilTest
 * @package App\Tests\Util
 */
class DashboardUtilTest extends TestCase
{
    /**
     * @var DashboardUtil
     */
    private $dashboardUtil;

    /**
     * @var JsonUtil|\PHPUnit\Framework\MockObject\MockObject
     */
    private $jsonUtilMock;

    /**
     * @var ErrorManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $errorManagerMock;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $entityManagerMock;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // create an instance of DashboardUtil with the mocks
        $this->dashboardUtil = new DashboardUtil(
            $this->jsonUtilMock,
            $this->errorManagerMock,
            $this->entityManagerMock
        );
    }

    /**
     * Test the getDatabaseEntityCount method of DashboardUtil.
     */
    public function testGetDatabaseEntityCount(): void
    {
        $entityMock = $this->getMockBuilder(\stdClass::class)->getMock();
        $search_criteria = ['name' => 'John'];

        // set up expectations for EntityManager mock
        $repositoryMock = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with($search_criteria)
            ->willReturn(['entity1', 'entity2']);

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        // Act
        $result = $this->dashboardUtil->getDatabaseEntityCount($entityMock, $search_criteria);

        $this->assertEquals(2, $result);
    }

    /**
     * Test the isBrowserListFound method of DashboardUtil.
     */
    public function testIsBrowserListFound(): void
    {
        $json_contents = ['browser1', 'browser2'];

        // set up expectations for JsonUtil mock
        $this->jsonUtilMock->expects($this->once())
            ->method('getJson')
            ->with($this->stringContains('browser-list.json'))
            ->willReturn($json_contents);

        // act
        $result = $this->dashboardUtil->isBrowserListFound();

        $this->assertTrue($result);
    }
}
