<?php // hash function test class

    declare(strict_types=1);
    use PHPUnit\Framework\TestCase;
    use becwork\utils\HashUtils;

    // link hash utils class
    require_once("app/encryption/HashUtils.php");

    final class HashTest extends TestCase {

        // test blowfish hash
        public function test_main_hash(): void {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->gen_main_hash("test");
            $this->assertEquals($hash, "$2y$10$123sbrznvdzvchpj8z5p5eVspVSYEKrDeIU2Rz907rTjDhWl3bCH2");
        }

        // test hash validation (test with invalid hash)
        public function test_hash_validate_flase() {
            $hash_utils = new HashUtils();

            $state = $hash_utils->hash_validate("test", "shity shity");
            $this->assertEquals($state, false);
        }

        // test hash validation (test with valid hash, bcrypt cost 10)
        public function test_hash_validate_true() {
            $hash_utils = new HashUtils();

            $state = $hash_utils->hash_validate("test", "$2y$10$3Dlhb9Ju6/o0mec6JOy00OX.YzrYjczWf7bDh172SejdAVtQWFv2O");
            $this->assertEquals($state, true);
        }

        // test bcrypt hash
        public function test_bcrypt(): void {
            $hash_utils = new HashUtils();

            $hash = $hash_utils->gen_bcrypt("test", 10);
            $this->assertEquals($hash, crypt('test', $hash));
        }

        // test sha1 hash
        public function test_sha1(): void {
            $hash_utils = new HashUtils();

            $hash = $hash_utils->gen_sha1("test");
            $this->assertEquals($hash, "*94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29");
        }

        // test md5
        public function test_md5(): void {
            $hash_utils = new HashUtils();

            $hash = $hash_utils->gen_md5("test");
            $this->assertEquals($hash, "098f6bcd4621d373cade4e832627b4f6");
        }

        // test sha 256
        public function test_sha256(): void {
            $hash_utils = new HashUtils();

            $hash = $hash_utils->gen_sha256("test");
            $this->assertEquals($hash, "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08");
        }

        // test custom hash (with md4)
        public function test_custom_hash(): void {
            $hash_utils = new HashUtils();

            $hash = $hash_utils->custom_hash("test", "md4");
            $this->assertEquals($hash, "db346d691d7acc4dc2625db19f9e3f52");
        }
    }
?>
