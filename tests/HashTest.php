<?php // hash function test class

    declare(strict_types=1);
    use PHPUnit\Framework\TestCase;

    # link hash utils class
    require_once("framework/crypt/HashUtils.php");

    final class HashTest extends TestCase {

        // test blowfish hash
        public function testBlowFish() {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->genBlowFish("test");
            $this->assertEquals($hash, "$2y$10$123sbrznvdzvchpj8z5p5eVspVSYEKrDeIU2Rz907rTjDhWl3bCH2");
        }

        // test sha1 hash
        public function testSHA1() {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->genSHA1("test");
            $this->assertEquals($hash, "*94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29");
        }

        // test md5
        public function testMD5() {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->hashMD5("test");
            $this->assertEquals($hash, "098f6bcd4621d373cade4e832627b4f6");
        }

        // test sha 256
        public function testSHA256() {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->genSHA256("test");
            $this->assertEquals($hash, "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08");
        }

        // test custom hash (with md4)
        public function testCustomHash() {
            $hashUtils = new \becwork\utils\HashUtils();

            $hash = $hashUtils->customHash("test", "md4");
            $this->assertEquals($hash, "db346d691d7acc4dc2625db19f9e3f52");
        }
    }
?>