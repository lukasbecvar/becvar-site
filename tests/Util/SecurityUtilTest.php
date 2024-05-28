<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 *
 * @covers \App\Util\SecurityUtil
 *
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    private SecurityUtil $securityUtil;

    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
        parent::setUp();
    }

    /**
     * Data provider for escapeString method.
     *
     * @return array<array<string>> The test data
     */
    public function escapeStringDataProvider(): array
    {
        return [
            ['<script>alert("XSS");</script>', '&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;'],
            ['This is a test', 'This is a test'],
            ['<b>Hello</b>', '&lt;b&gt;Hello&lt;/b&gt;'],
        ];
    }

    /**
     * Data provider for genBcryptHash method.
     *
     * @return array<array<string|int>> The test data
     */
    public function genBcryptHashDataProvider(): array
    {
        return [
            ['password123', 10],
            ['anotherPassword', 12],
        ];
    }

    /**
     * Data provider for hashValidate method.
     *
     * @return array<array<string|bool>> The test data
     */
    public function hashValidateDataProvider(): array
    {
        return [
            ['password123', password_hash('password123', PASSWORD_BCRYPT), true],
            ['wrongPassword', password_hash('password123', PASSWORD_BCRYPT), false],
        ];
    }

    /**
     * Data provider for encryptAes method.
     *
     * @return array<array<string>> The test data
     */
    public function encryptAesDataProvider(): array
    {
        return [
            ['{"key": "value"}'],
            ['12345'],
            ['Testing encryption'],
        ];
    }

    /**
     * Test escapeString method.
     *
     * @dataProvider escapeStringDataProvider
     *
     * @param string $input The input string
     * @param string $expected The expected output string
     *
     * @return void
     */
    public function testEscapeString(string $input, string $expected): void
    {
        $result = $this->securityUtil->escapeString($input);

        // assert
        $this->assertSame($expected, $result);
    }

    /**
     * Test hashValidate method.
     *
     * @dataProvider hashValidateDataProvider
     *
     * @param string $plainText The plain text
     * @param string $hash The hash
     * @param bool $expected The expected result
     *
     * @return void
     */
    public function testHashValidate(string $plainText, string $hash, bool $expected): void
    {
        $result = $this->securityUtil->hashValidate($plainText, $hash);

        // assert
        $this->assertSame($expected, $result);
    }

    /**
     * Test genBcryptHash method.
     *
     * @dataProvider genBcryptHashDataProvider
     *
     * @param string $plainText The plain text
     * @param int $cost The cost
     *
     * @return void
     */
    public function testGenBcryptHash(string $plainText, int $cost): void
    {
        $result = $this->securityUtil->genBcryptHash($plainText, $cost);

        // assert
        $this->assertTrue(password_verify($plainText, $result));
    }

    /**
     * Test encryptAes method.
     *
     * @dataProvider encryptAesDataProvider
     *
     * @param string $plainText The plain text
     *
     * @return void
     */
    public function testEncryptAes(string $plainText): void
    {
        $encryptedData = $this->securityUtil->encryptAes($plainText);
        $decryptedData = $this->securityUtil->decryptAes($encryptedData);

        // assert
        $this->assertSame($plainText, $decryptedData);
    }
}
