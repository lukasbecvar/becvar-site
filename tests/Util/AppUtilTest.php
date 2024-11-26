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
 * Test cases for app util class
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
     * Test get environment variable value from .env file
     *
     * @return void
     */
    public function testGetEnvValue(): void
    {
        $_ENV['TEST_ENV'] = 'testValue';

        // call tested method
        $result = $this->appUtil->getEnvValue('TEST_ENV');

        // assert result
        $this->assertEquals('testValue', $result);
    }

    /**
     * Test get application root directory
     *
     * @return void
     */
    public function testGetAppRootDir(): void
    {
        // call tested method
        $result = $this->appUtil->getAppRootDir();

        // assert result
        $this->assertIsString($result);
    }

    /**
     * Test get HTTP host
     *
     * @return void
     */
    public function testGetHttpHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        // call tested method
        $result = $this->appUtil->getHttpHost();

        // assert result
        $this->assertEquals('localhost', $result);
    }

    /**
     * Test check if application is running on localhost
     *
     * @return void
     */
    public function testCheckIfApplicationIsRunningOnLocalhost(): void
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
     * Test check if connection is secure (SSL)
     *
     * @return void
     */
    public function testCheckIsConnectionSecure(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->appUtil->isSsl());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->appUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->appUtil->isSsl());
    }

    /**
     * Test check if application is in maintenance mode
     *
     * @return void
     */
    public function testCheckIsInMaintenanceMode(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->appUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->appUtil->isMaintenance());
    }

    /**
     * Test check if ssl only mode is enabled
     *
     * @return void
     */
    public function testCheckIsSSLOnlyModeEnabled(): void
    {
        $_ENV['SSL_ONLY'] = 'true';
        $this->assertTrue($this->appUtil->isSSLOnly());

        $_ENV['SSL_ONLY'] = 'false';
        $this->assertFalse($this->appUtil->isSSLOnly());
    }

    /**
     * Test check if application is in development mode
     *
     * @return void
     */
    public function testCheckIfApplicationIsInDevelopmentMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'test';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->appUtil->isDevMode());
    }

    /**
     * Test get value of a query string parameter, with XSS protection
     *
     * @return void
     */
    public function testGetValueOfAQueryStringParameterWithXSSProtection(): void
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
