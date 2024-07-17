<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 *
 * This class tests the SecurityUtil class
 *
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    private SecurityUtil $securityUtil;

    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
        $_ENV['APP_SECRET'] = 'test_secret';
    }

    /**
     * Test escape XSS attacks
     *
     * @return void
     */
    public function testEscapeString(): void
    {
        $input = '<script>alert("xss")</script>';
        $expectedOutput = '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;';

        // assert result
        $this->assertEquals($expectedOutput, $this->securityUtil->escapeString($input));
    }

    /**
     * Test hash validation
     *
     * @return void
     */
    public function testHashValidate(): void
    {
        $plainText = 'password123';
        $hash = password_hash($plainText, PASSWORD_BCRYPT);

        // assert result
        $this->assertTrue($this->securityUtil->hashValidate($plainText, $hash));
        $this->assertFalse($this->securityUtil->hashValidate('wrongpassword', $hash));
    }

    /**
     * Test generate bcrypt hash
     *
     * @return void
     */
    public function testGenBcryptHash(): void
    {
        $plainText = 'password123';
        $cost = 10;
        $hash = $this->securityUtil->genBcryptHash($plainText, $cost);

        // assert result
        $this->assertTrue(password_verify($plainText, $hash));
    }

    /**
     * Test encrypt AES
     *
     * @return void
     */
    public function testEncryptAes(): void
    {
        $plainText = 'my_secret_data';
        $encrypted = $this->securityUtil->encryptAes($plainText);

        // assert result
        $this->assertNotEquals($plainText, $encrypted);
    }

    /**
     * Test decrypt AES
     *
     * @return void
     */
    public function testDecryptAes(): void
    {
        $plainText = 'my_secret_data';
        $encrypted = $this->securityUtil->encryptAes($plainText);
        $decrypted = $this->securityUtil->decryptAes($encrypted);

        // assert result
        $this->assertEquals($plainText, $decrypted);
    }
}
