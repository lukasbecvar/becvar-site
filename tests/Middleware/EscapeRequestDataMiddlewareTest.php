<?php

namespace App\Tests\Middleware;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Service\Middleware\EscapeRequestDataMiddleware;

/**
 * Class EscapeRequestDataMiddlewareTest
 *
 * Testing class for EscapeRequestDataMiddleware.
 *
 * @package App\Tests\Middleware
 */
class EscapeRequestDataMiddlewareTest extends TestCase
{
    /**
     * Tests HTML character escaping in request input data.
     */
    public function testHtmlCharsEscape(): void
    {
        // create a mock Request object
        $request = Request::create('/', 'POST', ['param' => '<script>alert("XSS");</script>']);

        // create a mock RequestEvent object with the request
        $requestEvent = new RequestEvent(
            $this->createMock(\Symfony\Component\HttpKernel\HttpKernelInterface::class),
            $request,
            \Symfony\Component\HttpKernel\HttpKernelInterface::MAIN_REQUEST
        );

        // create an instance of the SecurityMiddleware
        $securityMiddleware = new EscapeRequestDataMiddleware(new SecurityUtil);

        // call the onKernelRequest method of the middleware
        $securityMiddleware->onKernelRequest($requestEvent);

        // retrieve the modified request
        $modifiedRequest = $requestEvent->getRequest();

        // assert that the input data in the request has been properly escaped
        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;', $modifiedRequest->get('param'));
    }
}
