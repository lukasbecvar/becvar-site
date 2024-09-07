<?php

namespace App\Tests\Manager;

use App\Entity\Visitor;
use App\Manager\BanManager;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class BanManagerTest
 *
 * BanManagerTest tests the BanManager class
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

        // create instance of BanManager
        $this->banManager = new BanManager(
            $this->logManager,
            $this->authManager,
            $this->errorManager,
            $this->visitorManager,
            $this->entityManager
        );
    }

    /**
     * Test if the unbanVisitor method updates the ban status and logs the action
     *
     * @return void
     */
    public function testUnbanVisitor(): void
    {
        $ipAddress = '127.0.0.1';
        $username = 'admin';

        // mock visitor
        $visitor = $this->createMock(Visitor::class);
        $visitor->expects($this->once())->method('setBannedStatus')->with('no')->willReturnSelf();

        // mock visitor manager
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // mock auth manager
        $this->authManager->method('getUsername')->willReturn($username);

        // mock log manager
        $this->logManager->expects($this->once())->method('log')->with(
            'ban-system',
            'visitor with ip: ' . $ipAddress . ' unbanned by ' . $username
        );

        // mock entity manager
        $this->entityManager->expects($this->once())->method('flush');

        // call method
        $this->banManager->unbanVisitor($ipAddress);
    }

    /**
     * Test if the isVisitorBanned method returns true when the visitor is banned
     *
     * @return void
     */
    public function testIsVisitorBanned(): void
    {
        $ipAddress = '127.0.0.1';

        // mock visitor
        $visitor = $this->createMock(Visitor::class);
        $visitor->method('getBannedStatus')->willReturn('yes');

        // mock visitor manager
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // assert output
        $this->assertTrue($this->banManager->isVisitorBanned($ipAddress));
    }

    /**
     * Test if the getBanReason method returns the correct ban reason
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

        // mock visitor manager
        $this->visitorManager->method('getVisitorRepository')->with($ipAddress)->willReturn($visitor);

        // assert output
        $this->assertEquals($reason, $this->banManager->getBanReason($ipAddress));
    }
}
