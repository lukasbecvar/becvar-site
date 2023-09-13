<?php // encryption function test class

    declare(strict_types=1);
    use PHPUnit\Framework\TestCase;
    use becwork\utils\CryptUtils;
    
    // link crypt utils class
    require_once("app/encryption/CryptUtils.php");

    final class CryptTest extends TestCase {

        // test base64 encode
        public function test_base64_encode(): void {
            $crypt_utils = new CryptUtils();

            $encode = $crypt_utils->gen_base64("test");
            $this->assertEquals($encode, "dGVzdA==");
        }

        // test base64 decode
        public function test_base64_decode(): void {
            $crypt_utils = new CryptUtils();

            $decode = $crypt_utils->decode_base64("dGVzdA==");
            $this->assertEquals($decode, "test");
        }

        // test AES-128-CBC encryption
        public function test_aes_encryption(): void {
            $crypt_utils = new CryptUtils();

            // testing string
            $string = "test";

            // encrypt string
            $encrypted = $crypt_utils->encrypt_aes($string, "1234", "aes-128-cbc");

            // decrypt string
            $decrypted = $crypt_utils->decrypt_aes($encrypted, "1234", "aes-128-cbc");

            $this->assertEquals($decrypted, $string);
        }
    }
?>
