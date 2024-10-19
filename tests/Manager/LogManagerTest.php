<?php

namespace App\Tests\Manager;

use App\Util\JsonUtil;
use App\Util\CookieUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Manager\VisitorManager;
use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class LogManagerTest
 *
 * Test the log manager class
 *
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    /**
     * Test save log
     *
     * @return void
     */
    public function testLog(): void
    {
        // init dependencies
        /** @var JsonUtil $jsonUtil */
        $jsonUtil = $this->createMock(JsonUtil::class);

        /** @var CookieUtil $cookieUtil */
        $cookieUtil = $this->createMock(CookieUtil::class);

        /** @var ErrorManager $errorManager */
        $errorManager = $this->createMock(ErrorManager::class);

        /** @var SecurityUtil & MockObject $securityUtil */
        $securityUtil = $this->createMock(SecurityUtil::class);

        /** @var LogRepository & MockObject $logRepository */
        $logRepository = $this->createMock(LogRepository::class);

        /** @var VisitorManager & MockObject $visitorManager */
        $visitorManager = $this->createMock(VisitorManager::class);

        /** @var VisitorInfoUtil & MockObject $visitorInfoUtil */
        $visitorInfoUtil = $this->createMock(VisitorInfoUtil::class);

        /** @var EntityManagerInterface & MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // set up EntityManager mock to expect method calls
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        // mock the VisitorManager to return a visitor ID
        $visitorManager->expects($this->once())->method('getVisitorID')->willReturn(1);

        // mock the VisitorInfoUtil to return a browser and IP address
        $visitorInfoUtil->expects($this->once())->method('getUserAgent')->willReturn('Mozilla/5.0');
        $visitorInfoUtil->expects($this->once())->method('getIP')->willReturn('127.0.0.1');

        // mock the SecurityUtil to escape strings
        $securityUtil->expects($this->exactly(4))->method('escapeString')
            ->willReturnCallback(function ($value) {
                return htmlspecialchars($value, ENT_QUOTES);
            });

        // instantiate LogManager with mocked dependencies
        $logManager = new LogManager(
            $jsonUtil,
            $cookieUtil,
            $errorManager,
            $securityUtil,
            $logRepository,
            $visitorManager,
            $visitorInfoUtil,
            $entityManager
        );

        // call log method with test data
        $logManager->log('test_name', 'test_value');
    }
}
