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
    /** tested middleware */
    private MaintenanceMiddleware $middleware;

    private SiteUtil & MockObject $siteUtilMock;
    private LoggerInterface & MockObject $loggerMock;
    private ErrorManager & MockObject $errorManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->siteUtilMock = $this->createMock(SiteUtil::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        // create instance of MaintenanceMiddleware
        $this->middleware = new MaintenanceMiddleware(
            $this->siteUtilMock,
            $this->loggerMock,
            $this->errorManagerMock
        );
    }

    /**
     * Test if the maintenance mode is enabled
     *
     * @return void
     */
    public function testRequestWhenMaintenanceModeEnabled(): void
    {
        // mock the site util
        $this->siteUtilMock->expects($this->once())->method('isMaintenance')->willReturn(true);

        // create a mock request event
        /** @var RequestEvent&MockObject $event */
        $event = $this->createMock(RequestEvent::class);

        // mock the error manager
        $this->errorManagerMock->expects($this->once())
            ->method('getErrorView')
            ->with('maintenance')
            ->willReturn('Maintenance Mode Content');

        // expect the response
        $event->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function ($response) {
                return $response instanceof Response &&
                    $response->getStatusCode() === 503 &&
                    $response->getContent() === 'Maintenance Mode Content';
            }));

        // call the middleware method
        $this->middleware->onKernelRequest($event);
    }

    /**
     * Test if the maintenance mode is disabled
     *
     * @return void
     */
    public function testRequestWhenMaintenanceModeDisabled(): void
    {
        // mock the site util
        $this->siteUtilMock->expects($this->once())->method('isMaintenance')->willReturn(false);

        // create a mock request event
        /** @var RequestEvent&MockObject $event */
        $event = $this->createMock(RequestEvent::class);

        // expect the error manager to not be called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // expect the response to be empty
        $event->expects($this->never())->method('setResponse');

        // call the middleware method
        $this->middleware->onKernelRequest($event);
    }
}
