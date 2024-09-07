<?php

namespace App\Tests\Manager;

use App\Util\SecurityUtil;
use App\Manager\ErrorManager;
use App\Manager\VisitorManager;
use PHPUnit\Framework\TestCase;
use App\Manager\MessagesManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class MessagesManagerTest
 *
 * Test the messages manager class
 *
 * @package App\Tests\Manager
 */
class MessagesManagerTest extends TestCase
{
    /**
     * Test save message
     *
     * @return void
     */
    public function testSaveMessage(): void
    {
        // mock dependencies
        /** @var SecurityUtil & MockObject $securityUtil */
        $securityUtil = $this->createMock(SecurityUtil::class);

        /** @var ErrorManager $errorManager */
        $errorManager = $this->createMock(ErrorManager::class);

        /** @var VisitorManager & MockObject $visitorManager */
        $visitorManager = $this->createMock(VisitorManager::class);

        /** @var EntityManagerInterface & MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // mock EntityManager to expect method calls
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        // mock VisitorManager behavior
        $visitorManager->expects($this->once())->method('updateVisitorEmail');

        // mock SecurityUtil to encrypt message
        $securityUtil->expects($this->once())->method('encryptAes')->willReturn('encrypted_message');

        // instantiate MessagesManager with mocked dependencies
        $messagesManager = new MessagesManager(
            $securityUtil,
            $errorManager,
            $visitorManager,
            $entityManager
        );

        // call saveMessage method with test data
        $result = $messagesManager->saveMessage(
            name: 'John Doe',
            email: 'john@example.com',
            messageInput: 'Hello World',
            ipAddress: '127.0.0.1',
            visitorId: '123'
        );

        // assert that the method returns true upon successful save
        $this->assertTrue($result);
    }
}
