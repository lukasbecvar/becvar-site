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
 * Test cases for log manager component
 *
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    private LogManager $logManager;
    private JsonUtil & MockObject $jsonUtil;
    private CookieUtil & MockObject $cookieUtil;
    private ErrorManager & MockObject $errorManager;
    private SecurityUtil & MockObject $securityUtil;
    private LogRepository & MockObject $logRepository;
    private VisitorManager & MockObject $visitorManager;
    private VisitorInfoUtil & MockObject $visitorInfoUtil;
    private EntityManagerInterface & MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->jsonUtil = $this->createMock(JsonUtil::class);
        $this->cookieUtil = $this->createMock(CookieUtil::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->securityUtil = $this->createMock(SecurityUtil::class);
        $this->logRepository = $this->createMock(LogRepository::class);
        $this->visitorManager = $this->createMock(VisitorManager::class);
        $this->visitorInfoUtil = $this->createMock(VisitorInfoUtil::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // create log manager instance
        $this->logManager = new LogManager(
            $this->jsonUtil,
            $this->cookieUtil,
            $this->errorManager,
            $this->securityUtil,
            $this->logRepository,
            $this->visitorManager,
            $this->visitorInfoUtil,
            $this->entityManager
        );
    }

    /**
     * Test save log to database
     *
     * @return void
     */
    public function testSaveLog(): void
    {
        // expect entity manager persist and flush calls
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        // expect get visitor info
        $this->visitorManager->expects($this->once())->method('getVisitorID')->willReturn(1);
        $this->visitorInfoUtil->expects($this->once())->method('getUserAgent')->willReturn('Mozilla/5.0');
        $this->visitorInfoUtil->expects($this->once())->method('getIP')->willReturn('127.0.0.1');

        // expect escape log message
        $this->securityUtil->expects($this->exactly(4))->method('escapeString')->willReturnCallback(function ($value) {
            return htmlspecialchars($value, ENT_QUOTES);
        });

        // call tested method
        $this->logManager->log('test_name', 'test_value');
    }

    /**
     * Test send log to external log api
     *
     * @return void
     */
    public function testExternalLog(): void
    {
        // set external log config
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'true';
        $_ENV['EXTERNAL_LOG_URL'] = 'http://becvar.xyz/log';
        $_ENV['EXTERNAL_LOG_TOKEN'] = 'test-token';

        // log message
        $value = 'This is a test log message';

        // expect json util get json call
        $this->jsonUtil->expects($this->once())->method('getJson')->with(
            $this->stringContains('http://becvar.xyz/log?token=test-token&name=becvar-site%3A+log&message=becvar-site%3A+This+is+a+test+log+message&level=4'),
            'POST'
        );

        // call tested method
        $this->logManager->externalLog($value);
    }

    /**
     * Test get logs count
     *
     * @return void
     */
    public function testGetLogsCount(): void
    {
        // mock count method
        $this->logRepository->expects($this->once())->method('count')->willReturn(10);

        // call tested method
        $result = $this->logManager->getLogsCount('test_status');

        // assert result
        $this->assertIsInt($result);
        $this->assertEquals(10, $result);
    }

    /**
     * Test get login logs count
     *
     * @return void
     */
    public function testGetLoginLogsCount(): void
    {
        // mock count method
        $this->logRepository->expects($this->once())->method('count')->willReturn(10);

        // call tested method
        $result = $this->logManager->getLoginLogsCount();

        // assert result
        $this->assertIsInt($result);
        $this->assertEquals(10, $result);
    }

    /**
     * Test check if logs are enabled
     *
     * @return void
     */
    public function testCheckisLogsEnabled(): void
    {
        // set logs enabled
        $_ENV['LOGS_ENABLED'] = 'true';

        // call tested method
        $result = $this->logManager->isLogsEnabled();

        // assert result
        $this->assertTrue($result);
    }

    /**
     * Test check if anti-log cookie is enabled
     *
     * @return void
     */
    public function testCheckIfEnabledAntiLog(): void
    {
        // call tested method
        $result = $this->logManager->isEnabledAntiLog();

        // assert result
        $this->assertIsBool($result);
    }

    /**
     * Test get log level
     *
     * @return void
     */
    public function testGetLogLevel(): void
    {
        // set log level
        $_ENV['LOG_LEVEL'] = 3;

        // call tested method
        $result = $this->logManager->getLogLevel();

        // assert result
        $this->assertIsInt($result);
        $this->assertEquals(3, $result);
    }
}
