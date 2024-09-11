<?php

namespace App\Tests\Util;

use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SessionUtilTest
 *
 * Test the SessionUtil class
 *
 * @package App\Tests\Util
 */
class SessionUtilTest extends TestCase
{
    private SessionUtil $sessionUtil;
    private SecurityUtil & MockObject $securityUtilMock;
    private ErrorManager & MockObject $errorManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        // create instance of SessionUtil
        $this->sessionUtil = new SessionUtil($this->securityUtilMock, $this->errorManagerMock);
    }

    /**
     * Test start session
     *
     * @return void
     */
    public function testStartSession(): void
    {
        // ensure that the session is not started and headers are not sent
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // start the session and check the status
        $this->sessionUtil->startSession();

        // assert that the session was started
        $this->assertEquals(1, session_status());
    }

    /**
     * Test check session value
     *
     * @return void
     */
    public function testCheckSession(): void
    {
        // start the session
        $this->sessionUtil->startSession();

        // set a session value
        $_SESSION['test'] = 'value';

        // assert session exists
        $this->assertTrue($this->sessionUtil->checkSession('test'));
        $this->assertFalse($this->sessionUtil->checkSession('nonexistent'));
    }

    /**
     * Test set session value
     *
     * @return void
     */
    public function testSetSession(): void
    {
        // session values
        $sessionName = 'test';
        $sessionValue = 'value';
        $encryptedValue = 'encrypted_value';

        // mock the encryptAes method
        $this->securityUtilMock->expects($this->once())
            ->method('encryptAes')->with($sessionValue)->willReturn($encryptedValue);

        // set the session
        $this->sessionUtil->setSession($sessionName, $sessionValue);

        // assert that the session was set
        $this->assertEquals($encryptedValue, $_SESSION[$sessionName]);
    }

    /**
     * Test get session value
     *
     * @return void
     */
    public function testGetSessionValue(): void
    {
        // session values
        $sessionName = 'test';
        $encryptedValue = 'encrypted_value';
        $decryptedValue = 'value';

        // start the session
        $this->sessionUtil->startSession();

        // set session value
        $_SESSION[$sessionName] = $encryptedValue;

        // mock the decryptAes method
        $this->securityUtilMock->expects($this->once())
            ->method('decryptAes')->with($encryptedValue)->willReturn($decryptedValue);

        // get the session value
        $value = $this->sessionUtil->getSessionValue($sessionName);

        // assert that the session was set
        $this->assertEquals($decryptedValue, $value);
    }
}
