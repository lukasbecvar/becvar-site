<?php

namespace App\Tests\Middleware;

use App\Manager\AuthManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Middleware\AuthentificatedCheckMiddleware;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AuthentificatedCheckMiddlewareTest
 *
 * Tests for the AuthentificatedCheckMiddleware class
 *
 * @package App\Tests\Middleware
 */
class AuthentificatedCheckMiddlewareTest extends TestCase
{
    /**
     * Test that the onKernelRequest method redirects to the login page when the user is not logged in
     *
     * @return void
     */
    public function testOnKernelRequestRedirectsToLoginPageWhenUserIsNotLoggedIn(): void
    {
        // mock AuthManager
        $authManager = $this->createMock(AuthManager::class);
        $authManager->expects($this->once())->method('isUserLogedin')->willReturn(false);

        // mock UrlGeneratorInterface
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())->method('generate')->with('auth_login')->willReturn('/login');

        // create middleware instance
        $middleware = new AuthentificatedCheckMiddleware($authManager, $urlGenerator);

        // create RequestEvent with a request to an admin route
        $request = Request::create('/admin/dashboard', 'GET');
        $event = new RequestEvent($this->createMock(\Symfony\Component\HttpKernel\HttpKernelInterface::class), $request, 1);

        // call the onKernelRequest method
        $middleware->onKernelRequest($event);

        // get the response from the event
        $response = $event->getResponse();

        // assert that a RedirectResponse to the login page is set in the event's response
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());

        // get the target URL of the RedirectResponse
        $targetUrl = $response->headers->get('Location');

        // assert that the target URL is the login page
        $this->assertEquals('/login', $targetUrl);
    }

    /**
     * Test that the onKernelRequest method does not redirect when the user is logged in
     *
     * @return void
     */
    public function testOnKernelRequestDoesNotRedirectWhenUserIsLoggedIn(): void
    {
        // mock AuthManager
        $authManager = $this->createMock(AuthManager::class);
        $authManager->expects($this->once())->method('isUserLogedin')->willReturn(true);

        // mock UrlGeneratorInterface - it should not be called
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->never())->method('generate');

        // create middleware instance
        $middleware = new AuthentificatedCheckMiddleware($authManager, $urlGenerator);

        // create RequestEvent with a request to an admin route
        $request = Request::create('/admin/dashboard', 'GET');
        $event = new RequestEvent($this->createMock(\Symfony\Component\HttpKernel\HttpKernelInterface::class), $request, 1);

        // call the onKernelRequest method
        $middleware->onKernelRequest($event);

        // assert that no RedirectResponse is set in the event's response
        $this->assertNull($event->getResponse());
    }
}
