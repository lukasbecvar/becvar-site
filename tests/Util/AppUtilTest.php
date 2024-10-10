<?php

namespace App\Tests\Util;

use App\Util\AppUtil;
use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppUtilTest
 *
 * This class tests the AppUtil class
 *
 * @package App\Tests\Util
 */
class AppUtilTest extends TestCase
{
    private AppUtil $appUtil;
    private SecurityUtil & MockObject $securityUtilMock;
    private KernelInterface & MockObject $kernelInterfaceMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);

        // mock kernel interface
        $this->kernelInterfaceMock = $this->createMock(KernelInterface::class);

        // create instance of AppUtil
        $this->appUtil = new AppUtil($this->securityUtilMock, $this->kernelInterfaceMock);
    }

    /**
     * Test get HTTP host
     *
     * @return void
     */
    public function testGetHttpHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertEquals('localhost', $this->appUtil->getHttpHost());
    }

    /**
     * Test is running localhost
     *
     * @return void
     */
    public function testIsRunningLocalhost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertTrue($this->appUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $this->assertTrue($this->appUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = '10.0.0.93';
        $this->assertTrue($this->appUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertFalse($this->appUtil->isRunningLocalhost());
    }

    /**
     * Test is SSL
     *
     * @return void
     */
    public function testIsSsl(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->appUtil->isSsl());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->appUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->appUtil->isSsl());
    }

    /**
     * Test is maintenance
     *
     * @return void
     */
    public function testIsMaintenance(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->appUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->appUtil->isMaintenance());
    }

    /**
     * Test is SSL only
     *
     * @return void
     */
    public function testIsSslOnly(): void
    {
        $_ENV['SSL_ONLY'] = 'true';
        $this->assertTrue($this->appUtil->isSSLOnly());

        $_ENV['SSL_ONLY'] = 'false';
        $this->assertFalse($this->appUtil->isSSLOnly());
    }

    /**
     * Test is dev mode
     *
     * @return void
     */
    public function testIsDevMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'test';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->appUtil->isDevMode());
    }

    /**
     * Test get query string
     *
     * @return void
     */
    public function testGetQueryString(): void
    {
        $query = 'test';
        $value = 'testValue';
        $escapedValue = 'escapedTestValue';

        $request = new Request([], [], [], [], [], [], null);
        $request->query->set($query, $value);

        // mock security util
        $this->securityUtilMock->method('escapeString')->with($value)->willReturn($escapedValue);

        // assert result
        $this->assertEquals($escapedValue, $this->appUtil->getQueryString($query, $request));
    }
}
