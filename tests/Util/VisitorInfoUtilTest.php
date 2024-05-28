<?php

namespace Tests\Unit\Util;

use App\Util\SiteUtil;
use App\Util\JsonUtil;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorInfoUtilTest
 *
 * @covers \App\Util\VisitorInfoUtil
 *
 * @package Tests\Unit\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    protected VisitorInfoUtil $visitorInfoUtil;

    protected function setUp(): void
    {
        parent::setUp();
        // mock dependencies
        $siteUtil = $this->createMock(SiteUtil::class);
        $siteUtil->method('isRunningLocalhost')->willReturn(false);

        $jsonUtil = $this->createMock(JsonUtil::class);
        $jsonUtil->method('getJson')->willReturn([
            'Chrome' => 'Google Chrome',
            'Firefox' => 'Mozilla Firefox'
        ]);

        // create instance of VisitorInfoUtil with mocked dependencies
        $this->visitorInfoUtil = new VisitorInfoUtil($siteUtil, $jsonUtil);
    }

    /**
     * Test getIP method
     *
     * @return void
     */
    public function testGetIP(): void
    {
        // test with known IP addresses
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
        $this->assertEquals('192.168.1.1', $this->visitorInfoUtil->getIP());

        // test with unknown IP addresses
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.2';
        unset($_SERVER['HTTP_CLIENT_IP']);
        $this->assertEquals('192.168.1.2', $this->visitorInfoUtil->getIP());

        // test with unknown IP addresses
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '192.168.1.3';
        $this->assertEquals('192.168.1.3', $this->visitorInfoUtil->getIP());
    }

    /**
     * Test getCountry method
     *
     * @return void
     */
    public function testGetBrowser(): void
    {
        // test with known user agent
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.999 Safari/537.36';
        $this->assertEquals('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.999 Safari/537.36', $this->visitorInfoUtil->getBrowser());
    }

    /**
     * Test getBrowserShortify method
     *
     * @return void
     */
    public function testGetBrowserShortify(): void
    {
        // test with known user agent
        $this->assertEquals('Chrome', $this->visitorInfoUtil->getBrowserShortify('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.999 Safari/537.36'));

        // test with unknown user agent
        $this->assertEquals('Unknown', $this->visitorInfoUtil->getBrowserShortify('Unknown User Agent'));
    }

    /**
     * Test getOS method
     *
     * @return void
     */
    public function testGetOS(): void
    {
        // test with known user agent
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.999 Safari/537.36';
        $this->assertEquals('Windows', $this->visitorInfoUtil->getOS());
    }
}
