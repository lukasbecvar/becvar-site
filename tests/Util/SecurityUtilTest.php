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
     * @return array<array<string>>
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
     * @return array<array<string|int>>
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
     * @return array<array<string|bool>>
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
     * @return array<array<string>>
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

        // assert
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider hashValidateDataProvider
     * @param string $plain_text
     * @param string $hash
     * @param bool $expected
     */
    public function testHashValidate(string $plain_text, string $hash, bool $expected): void
    {
        $result = $this->securityUtil->hashValidate($plain_text, $hash);

        // assert
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider genBcryptHashDataProvider
     * @param string $plain_text
     * @param int $cost
     */
    public function testGenBcryptHash(string $plain_text, int $cost): void
    {
        $result = $this->securityUtil->genBcryptHash($plain_text, $cost);

        // assert
        $this->assertTrue(password_verify($plain_text, $result));
    }

    /**
     * @dataProvider encryptAesDataProvider
     * @param string $plain_text
     */
    public function testEncryptAes(string $plain_text): void
    {
        $encrypted_data = $this->securityUtil->encryptAes($plain_text);
        $decrypted_data = $this->securityUtil->decryptAes($encrypted_data);

        // assert
        $this->assertSame($plain_text, $decrypted_data);
    }
}
