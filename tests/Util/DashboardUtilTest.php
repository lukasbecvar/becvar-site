<?php

namespace Tests\Unit\Util;

use App\Util\JsonUtil;
use App\Util\DashboardUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DashboardUtilTest
 *
 * Test cases for DashboardUtil
 *
 * @package Tests\Unit\Util
 */
class DashboardUtilTest extends TestCase
{
    protected DashboardUtil $dashboardUtil;

    protected function setUp(): void
    {
        parent::setUp();
        // mock dependencies
        $jsonUtilMock = $this->createMock(JsonUtil::class);
        $errorManagerMock = $this->createMock(ErrorManager::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // create instance of DashboardUtil
        $this->dashboardUtil = new DashboardUtil($jsonUtilMock, $errorManagerMock, $entityManagerMock);
    }

    /**
     * Test getHostUptime method
     *
     * @return void
     */
    public function testGetHostUptime(): void
    {
        // call the method being tested
        $hostUptime = $this->dashboardUtil->getHostUptime();

        // assert that the result is a non-empty string
        $this->assertIsString($hostUptime);
        $this->assertNotEmpty($hostUptime);
    }

    /**
     * Test getHostLoadAverage method
     *
     * @return void
     */
    public function testGetCpuUsage(): void
    {
        // call the method being tested
        $cpuUsage = $this->dashboardUtil->getCpuUsage();

        // assert that the result is a float between 0 and 100
        $this->assertIsFloat($cpuUsage);
        $this->assertGreaterThanOrEqual(0, $cpuUsage);
        $this->assertLessThanOrEqual(100, $cpuUsage);
    }

    /**
     * Test getRamUsage method
     *
     * @return void
     */
    public function testGetRamUsage(): void
    {
        // call the method being tested
        $ramUsage = $this->dashboardUtil->getRamUsage();

        // assert that the result is an array with keys 'used', 'free', and 'total'
        $this->assertIsArray($ramUsage);
        $this->assertArrayHasKey('used', $ramUsage);
        $this->assertArrayHasKey('free', $ramUsage);
        $this->assertArrayHasKey('total', $ramUsage);
    }

    /**
     * Test getDiskUsage method
     *
     * @return void
     */
    public function testGetSoftwareInfo(): void
    {
        // call the method being tested
        $softwareInfo = $this->dashboardUtil->getSoftwareInfo();

        // assert that the result is an array with keys 'packages' and 'distro'
        $this->assertIsArray($softwareInfo);
        $this->assertArrayHasKey('packages', $softwareInfo);
        $this->assertArrayHasKey('distro', $softwareInfo);
    }

    /**
     * Test getDiskUsage method
     *
     * @return void
     */
    public function testIsWebUserSudo(): void
    {
        // call the method being tested
        $isSudo = $this->dashboardUtil->isWebUserSudo();

        // assert that the result is a boolean value
        $this->assertIsBool($isSudo);
    }

    /**
     * Test getDiskUsage method
     *
     * @return void
     */
    public function testIsSystemLinux(): void
    {
        // call the method being tested
        $isLinux = $this->dashboardUtil->isSystemLinux();

        // assert that the result is a boolean value
        $this->assertIsBool($isLinux);
    }
}
