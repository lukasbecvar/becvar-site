<?php

namespace App\Tests\Middleware;

use App\Util\AppUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\SecurityCheckMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityCheckMiddlewareTest
 *
 * Test for security check middleware
 *
 * @package App\Tests\Middleware
 */
class SecurityCheckMiddlewareTest extends TestCase
{
    private AppUtil & MockObject $appUtillMock;
    private SecurityCheckMiddleware $middleware;
    private ErrorManager & MockObject $errorManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtillMock = $this->createMock(AppUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        // create security check middleware instance
        $this->middleware = new SecurityCheckMiddleware(
            $this->appUtillMock,
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
        $this->appUtillMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // mock SSL connection is secure
        $this->appUtillMock->expects($this->once())->method('isSsl')->willReturn(true);

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // call middleware
        $this->middleware->onKernelRequest();
    }

    /**
     * Test SSL check fail
     *
     * @return void
     */
    public function testSslCheckFail(): void
    {
        // mock SSL check enabled
        $this->appUtillMock->expects($this->once())->method('isSSLOnly')->willReturn(true);

        // mock SSL connection is not secure
        $this->appUtillMock->expects($this->once())->method('isSsl')->willReturn(false);

        // expect error handling called with HTTP_UPGRADE_REQUIRED status
        $this->errorManagerMock->expects($this->once())->method('handleError')->with(
            'SSL error: connection not running on ssl protocol',
            Response::HTTP_UPGRADE_REQUIRED
        );

        // call middleware
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
        $this->appUtillMock->expects($this->once())->method('isSSLOnly')->willReturn(false);

        // expect no SSL check and no error handling called
        $this->appUtillMock->expects($this->never())->method('isSsl');

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // call middleware
        $this->middleware->onKernelRequest();
    }
}
