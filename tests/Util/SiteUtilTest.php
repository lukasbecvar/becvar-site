<?php

namespace App\Tests\Util;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SiteUtilTest
 *
 * This class tests the SiteUtil class
 *
 * @package App\Tests\Util
 */
class SiteUtilTest extends TestCase
{
    private SiteUtil $siteUtil;
    private SecurityUtil & MockObject $securityUtilMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);

        // create instance of SiteUtil
        $this->siteUtil = new SiteUtil($this->securityUtilMock);
    }

    /**
     * Test get HTTP host
     *
     * @return void
     */
    public function testGetHttpHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertEquals('localhost', $this->siteUtil->getHttpHost());
    }

    /**
     * Test is running localhost
     *
     * @return void
     */
    public function testIsRunningLocalhost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertTrue($this->siteUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $this->assertTrue($this->siteUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = '10.0.0.93';
        $this->assertTrue($this->siteUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertFalse($this->siteUtil->isRunningLocalhost());
    }

    /**
     * Test is SSL
     *
     * @return void
     */
    public function testIsSsl(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->siteUtil->isSsl());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->siteUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->siteUtil->isSsl());
    }

    /**
     * Test is maintenance
     *
     * @return void
     */
    public function testIsMaintenance(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->siteUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->siteUtil->isMaintenance());
    }

    /**
     * Test is SSL only
     *
     * @return void
     */
    public function testIsSslOnly(): void
    {
        $_ENV['SSL_ONLY'] = 'true';
        $this->assertTrue($this->siteUtil->isSSLOnly());

        $_ENV['SSL_ONLY'] = 'false';
        $this->assertFalse($this->siteUtil->isSSLOnly());
    }

    /**
     * Test is dev mode
     *
     * @return void
     */
    public function testIsDevMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->siteUtil->isDevMode());

        $_ENV['APP_ENV'] = 'test';
        $this->assertTrue($this->siteUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->siteUtil->isDevMode());
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
        $this->assertEquals($escapedValue, $this->siteUtil->getQueryString($query, $request));
    }
}
