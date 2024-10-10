<?php

namespace App\Tests\Util;

use App\Util\AppUtil;
use App\Util\JsonUtil;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class VisitorInfoUtilTest
 *
 * This class tests the VisitorInfoUtil class
 *
 * @package App\Tests\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    private VisitorInfoUtil $visitorInfoUtil;
    private AppUtil & MockObject $appUtilMock;
    private JsonUtil & MockObject $jsonUtilMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtilMock = $this->createMock(AppUtil::class);
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);

        // create instance of VisitorInfoUtil
        $this->visitorInfoUtil = new VisitorInfoUtil($this->appUtilMock, $this->jsonUtilMock);
    }

    /**
     * Test get visitor IP address
     *
     * @return void
     */
    public function testGetIp(): void
    {
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
        $this->assertEquals('192.168.1.1', $this->visitorInfoUtil->getIP());

        unset($_SERVER['HTTP_CLIENT_IP']);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.2';
        $this->assertEquals('192.168.1.2', $this->visitorInfoUtil->getIP());

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '192.168.1.3';
        $this->assertEquals('192.168.1.3', $this->visitorInfoUtil->getIP());
    }

    /**
     * Test get visitor browser
     *
     * @return void
     */
    public function testGetBrowser(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $this->assertEquals('Mozilla/5.0', $this->visitorInfoUtil->getUserAgent());

        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertEquals('Unknown', $this->visitorInfoUtil->getUserAgent());
    }

    /**
     * Test get visitor browser shortify name
     *
     * @return void
     */
    public function testGetBrowserShortify(): void
    {
        $browserList = [
            'Firefox' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'Chrome' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'
        ];

        // mock json util
        $this->jsonUtilMock->method('getJson')->willReturn($browserList);

        // assert result
        $this->assertEquals('Firefox', $this->visitorInfoUtil->getBrowserShortify('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0'));
        $this->assertEquals('Chrome', $this->visitorInfoUtil->getBrowserShortify('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'));
        $this->assertEquals('Unknown', $this->visitorInfoUtil->getBrowserShortify('Some Unknown Browser'));
    }

    /**
     * Test get visitor OS
     *
     * @return void
     */
    public function testGetOs(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0';
        $this->assertEquals('Windows', $this->visitorInfoUtil->getOS());

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15';
        $this->assertEquals('Mac OS X', $this->visitorInfoUtil->getOS());

        $_SERVER['HTTP_USER_AGENT'] = 'Some Unknown User Agent';
        $this->assertEquals('Unknown OS', $this->visitorInfoUtil->getOS());
    }

    /**
     * Test get visitor IP info
     *
     * @return void
     */
    public function testGetIpInfo(): void
    {
        // assert result
        $this->assertNotNull($this->visitorInfoUtil->getIpInfo('8.8.8.8'));
    }

    /**
     * Test get visitor location
     *
     * @return void
     */
    public function testGetLocation(): void
    {
        // mock site util
        $this->appUtilMock->method('isRunningLocalhost')->willReturn(true);

        // assert result
        $this->assertEquals(['city' => 'locale', 'country' => 'host'], $this->visitorInfoUtil->getLocation('127.0.0.1'));
    }
}
