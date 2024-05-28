<?php

namespace App\Tests\Middleware;

use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Manager\AuthManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\AutoLoginMiddleware;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AutoLoginMiddlewareTest
 *
 * Test the AutoLoginMiddleware class
 *
 * @package App\Tests\Middleware
 */
class AutoLoginMiddlewareTest extends TestCase
{
    private MockObject|AutoLoginMiddleware $middleware;
    private MockObject|CookieUtil $cookieUtilMock;
    private MockObject|SessionUtil $sessionUtilMock;
    private MockObject|AuthManager $authManagerMock;

    protected function setUp(): void
    {
        $this->cookieUtilMock = $this->createMock(CookieUtil::class);
        $this->sessionUtilMock = $this->createMock(SessionUtil::class);
        $this->authManagerMock = $this->createMock(AuthManager::class);

        $this->middleware = new AutoLoginMiddleware(
            $this->cookieUtilMock,
            $this->sessionUtilMock,
            $this->authManagerMock
        );
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testRequestWhenUserIsLoggedIn(): void
    {
        // mocking the isUserLogedin method to return true
        $this->authManagerMock->expects($this->once())->method('isUserLogedin')->willReturn(true);

        // mocking the get method of the cookieUtilMock to return a value
        $this->cookieUtilMock->expects($this->never())->method('get');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testRequestWhenCookieIsNotSet(): void
    {
        // mocking the isUserLogedin method to return false
        $this->authManagerMock->expects($this->once())->method('isUserLogedin')->willReturn(false);

        // mocking the get method of the cookieUtilMock to return null
        $this->cookieUtilMock->expects($this->never())->method('get');

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }
}
