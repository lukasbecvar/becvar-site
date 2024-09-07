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
 * Test the auto login middleware
 *
 * @package App\Tests\Middleware
 */
class AutoLoginMiddlewareTest extends TestCase
{
    /** tested middleware */
    private AutoLoginMiddleware $middleware;

    private CookieUtil & MockObject $cookieUtilMock;
    private SessionUtil & MockObject $sessionUtilMock;
    private AuthManager & MockObject $authManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->cookieUtilMock = $this->createMock(CookieUtil::class);
        $this->sessionUtilMock = $this->createMock(SessionUtil::class);
        $this->authManagerMock = $this->createMock(AuthManager::class);

        // create instance of AutoLoginMiddleware
        $this->middleware = new AutoLoginMiddleware(
            $this->cookieUtilMock,
            $this->sessionUtilMock,
            $this->authManagerMock,
        );
    }

    /**
     * Test already logged in user
     *
     * @return void
     */
    public function testRequestUserAlreadyLoggedIn(): void
    {
        // mock the auth manager
        $this->authManagerMock->expects($this->once())->method('isUserLogedin')->willReturn(true);

        // mock the url generator
        $this->cookieUtilMock->expects($this->never())->method('get');

        // call the middleware method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test cookie not set
     *
     * @return void
     */
    public function testRequestCookieNotSet(): void
    {
        // mock the auth manager
        $this->authManagerMock->expects($this->once())->method('isUserLogedin')->willReturn(false);

        // unser cookie token
        unset($_COOKIE['user-token']);

        // mock the cookie util
        $this->cookieUtilMock->expects($this->never())->method('get');

        // call the middleware method
        $this->middleware->onKernelRequest();
    }
}
