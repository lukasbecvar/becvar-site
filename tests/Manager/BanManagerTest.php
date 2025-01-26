<?php

namespace App\Tests\Manager;

use App\Entity\Visitor;
use App\Manager\BanManager;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Manager\VisitorManager;
use App\Repository\VisitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class BanManagerTest
 *
 * Test cases for ban manager component
 *
 * @package App\Tests\Manager
 */
class BanManagerTest extends TestCase
{
    private BanManager $banManager;
    private LogManager & MockObject $logManager;
    private AuthManager & MockObject $authManager;
    private ErrorManager & MockObject $errorManager;
    private VisitorManager & MockObject $visitorManager;
    private EntityManagerInterface & MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->logManager = $this->createMock(LogManager::class);
        $this->authManager = $this->createMock(AuthManager::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->visitorManager = $this->createMock(VisitorManager::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // create ban manager instance
        $this->banManager = new BanManager(
            $this->logManager,
            $this->authManager,
            $this->errorManager,
            $this->visitorManager,
            $this->entityManager
        );
    }

    /**
     * Test unban banned visitor
     *
     * @return void
     */
    public function testUnbanVisitor(): void
    {
        $ipAddress = '127.0.0.1';
        $username = 'admin';

        // mock visitor
        $visitor = $this->createMock(Visitor::class);
        $visitor->expects($this->once())->method('setBannedStatus')->with(false)->willReturnSelf();

        // mock get visitor repository
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // mock get admin username (who unbanning the visitor)
        $this->authManager->method('getUsername')->willReturn($username);

        // expect log call
        $this->logManager->expects($this->once())->method('log')->with(
            name: 'ban-system',
            value: 'visitor with ip: ' . $ipAddress . ' unbanned by ' . $username
        );

        // expect flush call
        $this->entityManager->expects($this->once())->method('flush');

        // call tested method
        $this->banManager->unbanVisitor($ipAddress);
    }

    /**
     * Test check if visitor is banned
     *
     * @return void
     */
    public function testIsVisitorBanned(): void
    {
        $ipAddress = '127.0.0.1';

        // mock visitor
        $visitor = $this->createMock(Visitor::class);
        $visitor->method('getBannedStatus')->willReturn(true);

        // mock visitor manager
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // call tested method
        $result = $this->banManager->isVisitorBanned($ipAddress);

        // assert result
        $this->assertTrue($result);
    }

    /**
     * Test get count of banned visitors
     *
     * @return void
     */
    public function testGetBannedCount(): void
    {
        $count = 10;

        // mock repository
        $repository = $this->createMock(VisitorRepository::class);
        $repository->method('count')->with(['banned_status' => 'yes'])->willReturn($count);

        // mock get visitor repository
        $this->entityManager->method('getRepository')->willReturn($repository);

        // call tested method
        $result = $this->banManager->getBannedCount();

        // assert result
        $this->assertEquals($count, $result);
    }

    /**
     * Test get ban reason of banned visitor
     *
     * @return void
     */
    public function testGetBanReason(): void
    {
        $ipAddress = '127.0.0.1';
        $reason = 'Test ban reason';

        // mock visitor
        $visitor = $this->createMock(Visitor::class);
        $visitor->method('getBanReason')->willReturn($reason);

        // mock get visitor repository
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // call tested method
        $result = $this->banManager->getBanReason($ipAddress);

        // assert result
        $this->assertEquals($reason, $result);
    }
}
