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
     * Test getHttpHost method with HTTP_HOST
     *
     * @return void
     */
    public function testGetHttpHost(): void
    {
        // mock $_SERVER['HTTP_HOST']
        $_SERVER['HTTP_HOST'] = 'example.com';

        // act
        $result = $this->siteUtil->getHttpHost();

        // assert
        $this->assertEquals('example.com', $result);
    }

    /**
     * Test getHttpHost method with SERVER_NAME
     *
     * @return void
     */
    public function testIsRunningLocalhost(): void
    {
        // mock $_SERVER['HTTP_HOST']
        $_SERVER['HTTP_HOST'] = 'localhost';

        // act
        $result = $this->siteUtil->isRunningLocalhost();

        // assert
        $this->assertTrue($result);
    }

    /**
     * Test getHttpHost method with SERVER_NAME
     *
     * @return void
     */
    public function testIsSsl(): void
    {
        // mock $_SERVER['HTTPS']
        $_SERVER['HTTPS'] = 'on';

        // act
        $result = $this->siteUtil->isSsl();

        // assert
        $this->assertTrue($result);
    }

    /**
     * Test isMaintenance method
     *
     * @return void
     */
    public function testIsMaintenance(): void
    {
        // mock $_ENV['MAINTENANCE_MODE']
        $_ENV['MAINTENANCE_MODE'] = 'true';

        // act
        $result = $this->siteUtil->isMaintenance();

        // assert
        $this->assertTrue($result);
    }

    /**
     * Test isDevMode method
     *
     * @return void
     */
    public function testIsDevMode(): void
    {
        // mock $_ENV['APP_ENV']
        $_ENV['APP_ENV'] = 'dev';

        // act
        $result = $this->siteUtil->isDevMode();

        // assert
        $this->assertTrue($result);
    }
}
