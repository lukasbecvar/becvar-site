<?php

namespace App\Tests\Middleware;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\SecurityCheckMiddleware;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SecurityCheckMiddlewareTest
 *
 * Test the SecurityCheckMiddleware class.
 *
 * @package App\Tests\Middleware
 */
class SecurityCheckMiddlewareTest extends TestCase
{
    private MockObject|SecurityCheckMiddleware $middleware;
    private MockObject|SiteUtil $siteUtilMock;
    private MockObject|ErrorManager $errorManagerMock;

    protected function setUp(): void
    {
        $this->siteUtilMock = $this->createMock(SiteUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        $this->middleware = new SecurityCheckMiddleware(
            $this->siteUtilMock,
            $this->errorManagerMock
        );
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWhenSSLCheckDisabled(): void
    {
        // expecting a call to isSSLOnly method
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(false);

        // expecting no call to error manager
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWhenSSLCheckEnabledAndConnectionIsSecure(): void
    {
        // expecting a call to isSSLOnly method
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // expecting a call to isSsl method
        $this->siteUtilMock->expects($this->once())->method('isSsl')->willReturn(true);

        // expecting no call to error manager
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWhenSSLCheckEnabledAndConnectionIsNotSecure(): void
    {
        // expecting a call to isSSLOnly method
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // expecting a call to isSsl method
        $this->siteUtilMock->expects($this->once())->method('isSsl')->willReturn(false);

        // expecting a call to error manager with specific error message
        $this->errorManagerMock->expects($this->once())->method('handleError')->with('SSL error: connection not running on ssl protocol', 500);

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }
}
