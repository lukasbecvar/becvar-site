<?php

namespace App\Tests\Util;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SiteUtilTest
 * 
 * @covers \App\Util\SiteUtil
 * 
 * @package App\Tests\Util
 */
class SiteUtilTest extends TestCase
{
    private SiteUtil $siteUtil;
    private SecurityUtil $securityUtilMock;

    protected function setUp(): void
    {
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);
        $this->siteUtil = new SiteUtil($this->securityUtilMock);

        parent::setUp();
    }

    /**
     * @covers \App\Util\SiteUtil::getHttpHost
     */
    public function testGetHttpHost(): void
    {
        // mock $_SERVER['HTTP_HOST']
        $_SERVER['HTTP_HOST'] = 'example.com';

        // act
        $result = $this->siteUtil->getHttpHost();

        $this->assertEquals('example.com', $result);
    }

    /**
     * @covers \App\Util\SiteUtil::isRunningLocalhost
     */
    public function testIsRunningLocalhost(): void
    {
        // mock $_SERVER['HTTP_HOST']
        $_SERVER['HTTP_HOST'] = 'localhost';

        // act
        $result = $this->siteUtil->isRunningLocalhost();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Util\SiteUtil::isSsl
     */
    public function testIsSsl(): void
    {
        // mock $_SERVER['HTTPS']
        $_SERVER['HTTPS'] = 'on';

        // act
        $result = $this->siteUtil->isSsl();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Util\SiteUtil::isMaintenance
     */
    public function testIsMaintenance(): void
    {
        // mock $_ENV['MAINTENANCE_MODE']
        $_ENV['MAINTENANCE_MODE'] = 'true';

        // act
        $result = $this->siteUtil->isMaintenance();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Util\SiteUtil::isDevMode
     */
    public function testIsDevMode(): void
    {
        // mock $_ENV['APP_ENV']
        $_ENV['APP_ENV'] = 'dev';

        // act
        $result = $this->siteUtil->isDevMode();

        $this->assertTrue($result);
    }
}
