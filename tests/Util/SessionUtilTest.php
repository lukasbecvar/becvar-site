<?php

namespace Tests\Unit\Util;

use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionUtilTest
 *
 * This class tests the SessionUtil class.
 *
 * @package Tests\Unit\Util
 */
class SessionUtilTest extends TestCase
{
    protected SessionUtil $sessionUtil;

    protected function setUp(): void
    {
        parent::setUp();
        // mock dependencies
        $securityUtil = $this->createMock(SecurityUtil::class);
        $securityUtil->method('encryptAes')->willReturn('encrypted_value');
        $securityUtil->method('decryptAes')->willReturn('decrypted_value');

        // Mock ErrorManager
        $errorManager = $this->createMock(ErrorManager::class);
        $errorManager->method('handleError')->willReturn(null);

        // create instance of SessionUtil with mocked dependencies
        $this->sessionUtil = new SessionUtil($securityUtil, $errorManager);
    }

    /**
     * Test startSession method
     *
     * @return void
     */
    public function testCheckSession(): void
    {
        // mock $_SESSION array
        $_SESSION['test_session'] = 'value';

        // call checkSession method
        $this->assertTrue($this->sessionUtil->checkSession('test_session'));
        $this->assertFalse($this->sessionUtil->checkSession('non_existent_session'));
    }

    /**
     * Test setSession method
     *
     * @return void
     */
    public function testSetSession(): void
    {
        // call setSession method
        $this->sessionUtil->setSession('test_session', 'value');

        // ensure session value is set correctly
        $this->assertEquals('encrypted_value', $_SESSION['test_session']);
    }

    /**
     * Test getSessionValue method
     *
     * @return void
     */
    public function testGetSessionValue(): void
    {
        // mock $_SESSION array
        $_SESSION['test_session'] = 'encrypted_value';

        // call getSessionValue method
        $value = $this->sessionUtil->getSessionValue('test_session');

        // ensure session value is decrypted correctly
        $this->assertEquals('decrypted_value', $value);
    }
}
