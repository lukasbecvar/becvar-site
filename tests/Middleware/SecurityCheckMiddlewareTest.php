<?php

namespace App\Tests\Middleware;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\SecurityCheckMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityCheckMiddlewareTest
 *
 * Test cases for SecurityCheckMiddleware class
 *
 * @package App\Tests\Middleware
 */
class SecurityCheckMiddlewareTest extends TestCase
{
    /** tested middleware */
    private SecurityCheckMiddleware $middleware;

    private SiteUtil & MockObject $siteUtilMock;
    private ErrorManager & MockObject $errorManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->siteUtilMock = $this->createMock(SiteUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        // create instance of SecurityCheckMiddleware
        $this->middleware = new SecurityCheckMiddleware(
            $this->siteUtilMock,
            $this->errorManagerMock
        );
    }

    /**
     * Test SSL check passes
     *
     * @return void
     */
    public function testSslCheckPasses(): void
    {
        // mock SSL check enabled
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // mock SSL connection is secure
        $this->siteUtilMock->expects($this->once())->method('isSsl')->willReturn(true);

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // execute method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test SSL check fails
     *
     * @return void
     */
    public function testSslCheckFails(): void
    {
        // mock SSL check enabled
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // mock SSL connection is not secure
        $this->siteUtilMock->expects($this->once())->method('isSsl')->willReturn(false);

        // expect error handling called with HTTP_UPGRADE_REQUIRED status
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')->with(
                'SSL error: connection not running on ssl protocol',
                Response::HTTP_UPGRADE_REQUIRED
            );

        // execute method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test SSL check disabled
     *
     * @return void
     */
    public function testSslCheckDisabled(): void
    {
        // mock SSL check disabled
        $this->siteUtilMock->expects($this->once())->method('isSSLOnly')->willReturn(false);

        // expect no SSL check and no error handling called
        $this->siteUtilMock->expects($this->never())->method('isSsl');

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // execute method
        $this->middleware->onKernelRequest();
    }
}
