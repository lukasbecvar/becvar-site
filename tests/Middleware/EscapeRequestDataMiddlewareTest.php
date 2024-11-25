<?php

namespace App\Tests\Middleware;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use App\Middleware\EscapeRequestDataMiddleware;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class EscapeRequestDataMiddlewareTest
 *
 * Test for escape request data middleware
 *
 * @package App\Tests\Middleware
 */
class EscapeRequestDataMiddlewareTest extends TestCase
{
    /**
     * Test escape request data (for security)
     *
     * @return void
     */
    public function testEscapeRequestData(): void
    {
        // arrange
        /** @var SecurityUtil & MockObject $securityUtil */
        $securityUtil = $this->createMock(SecurityUtil::class);
        $securityUtil->method('escapeString')->willReturnCallback(function ($value) {
            return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
        });

        // create a request with unescaped data
        $requestData = [
            'name' => '<script>alert("XSS Attack!");</script>',
            'message' => '<p>Hello, World!</p>',
            'email' => 'user@example.com'
        ];

        // create a request event
        $request = new Request([], $requestData);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        // create a request event
        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request $request */
        $event = new RequestEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        // call middleware
        $middleware = new EscapeRequestDataMiddleware($securityUtil);
        $middleware->onKernelRequest($event);

        // assert response
        $this->assertEquals('&lt;script&gt;alert(&quot;XSS Attack!&quot;);&lt;/script&gt;', $request->get('name'));
        $this->assertEquals('&lt;p&gt;Hello, World!&lt;/p&gt;', $request->get('message'));
        $this->assertEquals('user@example.com', $request->get('email'));
    }
}
