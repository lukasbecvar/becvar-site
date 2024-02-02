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
    /** @var SecurityUtil */
    private $securityUtil;

    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
        parent::setUp();
    }

    /**
     * Data provider for escapeString method.
     *
     * @return array<mixed>
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
     * @return array<mixed>
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
     * @return array<mixed>
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
     * @return array<mixed>
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
     * @dataProvider escapeStringDataProvider
     * @param string $input
     * @param string $expected
     */
    public function testEscapeString(string $input, string $expected): void
    {
        $result = $this->securityUtil->escapeString($input);
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider hashValidateDataProvider
     * @param string $plainText
     * @param string $hash
     * @param bool $expected
     */
    public function testHashValidate(string $plainText, string $hash, bool $expected): void
    {
        $result = $this->securityUtil->hashValidate($plainText, $hash);
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider genBcryptHashDataProvider
     * @param string $plainText
     * @param int $cost
     */
    public function testGenBcryptHash(string $plainText, int $cost): void
    {
        $result = $this->securityUtil->genBcryptHash($plainText, $cost);
        $this->assertTrue(password_verify($plainText, $result));
    }

    /**
     * @dataProvider encryptAesDataProvider
     * @param string $plainText
     */
    public function testEncryptAes(string $plainText): void
    {
        $encryptedData = $this->securityUtil->encryptAes($plainText);
        $decryptedData = $this->securityUtil->decryptAes($encryptedData);
        $this->assertSame($plainText, $decryptedData);
    }
}
