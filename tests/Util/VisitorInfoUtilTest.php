<?php

namespace App\Tests\Util;

use App\Util\JsonUtil;
use App\Util\SiteUtil;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\VisitorInfoUtil
 */
class VisitorInfoUtilTest extends TestCase
{
    /** @var VisitorInfoUtil */
    private $visitorInfoUtil;

    /** @var SiteUtil|\PHPUnit\Framework\MockObject\MockObject */
    private $siteUtilMock;

    /** @var JsonUtil|\PHPUnit\Framework\MockObject\MockObject */
    private $jsonUtilMock;

    protected function setUp(): void
    {
        $this->siteUtilMock = $this->createMock(SiteUtil::class);
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);
        $this->visitorInfoUtil = new VisitorInfoUtil($this->siteUtilMock, $this->jsonUtilMock);
    
        parent::setUp();
    }

    /**
     * @covers \App\Util\VisitorInfoUtil::getIP
     */
    public function testGetIp(): void
    {
        // mock $_SERVER['HTTP_CLIENT_IP']
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.0.1';

        // act
        $result = $this->visitorInfoUtil->getIP();

        $this->assertEquals('192.168.0.1', $result);
    }

    /**
     * @covers \App\Util\VisitorInfoUtil::getBrowser
     */
    public function testGetBrowser(): void
    {
        // mock $_SERVER['HTTP_USER_AGENT']
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)';

        // act
        $result = $this->visitorInfoUtil->getBrowser();

        $this->assertEquals('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)', $result);
    }
}
