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
        $_ENV['APP_SECRET'] = 'test_secret';

        // create instance of SecurityUtil
        $this->securityUtil = new SecurityUtil();
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
     * Test verify password
     *
     * @return void
     */
    public function testVerifyPassword(): void
    {
        $password = 'password123';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        // assert result
        $this->assertTrue($this->securityUtil->verifyPassword($password, $hash));
        $this->assertFalse($this->securityUtil->verifyPassword('wrongpassword', $hash));
    }

    /**
     * Test generate password hash
     *
     * @return void
     */
    public function testGenerateHash(): void
    {
        $plainText = 'password123';
        $hash = $this->securityUtil->generateHash($plainText);

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
