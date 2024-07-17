<?php

namespace App\Tests\Middleware;

use App\Util\SiteUtil;
use Psr\Log\LoggerInterface;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\MaintenanceMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class MaintenanceMiddlewareTest
 *
 * Test the maintenance middleware
 *
 * @package App\Tests\Middleware
 */
class MaintenanceMiddlewareTest extends TestCase
{
    private SiteUtil|MockObject $siteUtilMock;
    private LoggerInterface|MockObject $loggerMock;
    private ErrorManager|MockObject $errorManagerMock;

    protected function setUp(): void
    {
        $this->siteUtilMock = $this->createMock(SiteUtil::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
    }

    /**
     * Test if the maintenance mode is enabled
     *
     * @return void
     */
    public function testRequestWhenMaintenanceModeEnabled(): void
    {
        $this->siteUtilMock->expects($this->once())
            ->method('isMaintenance')
            ->willReturn(true);

        $middleware = new MaintenanceMiddleware(
            $this->siteUtilMock,
            $this->loggerMock,
            $this->errorManagerMock
        );

        $event = $this->createMock(RequestEvent::class);

        $this->errorManagerMock->expects($this->once())
            ->method('getErrorView')
            ->with('maintenance')
            ->willReturn('Maintenance Mode Content');

        $event->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function ($response) {
                return $response instanceof Response &&
                    $response->getStatusCode() === 503 &&
                    $response->getContent() === 'Maintenance Mode Content';
            }));

        $middleware->onKernelRequest($event);
    }

    /**
     * Test if the maintenance mode is disabled
     *
     * @return void
     */
    public function testRequestWhenMaintenanceModeDisabled(): void
    {
        $this->siteUtilMock->expects($this->once())
            ->method('isMaintenance')
            ->willReturn(false);

        $middleware = new MaintenanceMiddleware(
            $this->siteUtilMock,
            $this->loggerMock,
            $this->errorManagerMock
        );

        $event = $this->createMock(RequestEvent::class);

        $this->errorManagerMock->expects($this->never())
            ->method('handleError');

        $event->expects($this->never())
            ->method('setResponse');

        $middleware->onKernelRequest($event);
    }
}
