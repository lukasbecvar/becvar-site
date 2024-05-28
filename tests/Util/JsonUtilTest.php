<?php

namespace Tests\Unit\Util;

use App\Util\JsonUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class JsonUtilTest
 *
 * Test cases for JsonUtil
 *
 * @package Tests\Unit\Util
 */
class JsonUtilTest extends TestCase
{
    protected MockObject|JsonUtil $jsonUtil;

    protected function setUp(): void
    {
        parent::setUp();
        // create instance of JsonUtil
        $this->jsonUtil = new JsonUtil();
    }

    /**
     * Test getJson method with different targets
     *
     * @return void
     */
    public function testGetJsonFromFile(): void
    {
        // test with existing JSON file
        $expectedData = ['key' => 'value'];
        $filePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($filePath, json_encode($expectedData));

        // get JSON data from file
        $jsonData = $this->jsonUtil->getJson($filePath);

        // assert the data
        $this->assertEquals($expectedData, $jsonData);

        // clean up the test file
        unlink($filePath);
    }

    /**
     * Test getJson method with different targets
     *
     * @return void
     */
    public function testGetJsonWithInvalidTarget(): void
    {
        // test with invalid file path
        $jsonData = $this->jsonUtil->getJson('non_existent_file.json');
        $this->assertNull($jsonData);
    }

    /**
     * Test getJson method with different targets
     *
     * @return void
     */
    public function testGetJsonWithInvalidData(): void
    {
        // test with invalid JSON data
        $invalidJson = '{"key": "value"';
        $filePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($filePath, $invalidJson);

        // get JSON data from file
        $jsonData = $this->jsonUtil->getJson($filePath);
        $this->assertNull($jsonData);

        // clean up the test file
        unlink($filePath);
    }
}
