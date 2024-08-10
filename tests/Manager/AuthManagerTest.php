<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AuthManagerTest
 *
 * AuthManagerTest tests the AuthManager class
 *
 * @package App\Tests\Manager
 */
class AuthManagerTest extends TestCase
{
    private AuthManager $authManager;
    private LogManager|MockObject $logManager;
    private CookieUtil|MockObject $cookieUtil;
    private SessionUtil|MockObject $sessionUtil;
    private ErrorManager|MockObject $errorManager;
    private SecurityUtil|MockObject $securityUtil;
    private UserRepository|MockObject $userRepository;
    private VisitorManager|MockObject $visitorManager;
    private VisitorInfoUtil|MockObject $visitorInfoUtil;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->logManager = $this->createMock(LogManager::class);
        $this->cookieUtil = $this->createMock(CookieUtil::class);
        $this->sessionUtil = $this->createMock(SessionUtil::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->securityUtil = $this->createMock(SecurityUtil::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->visitorManager = $this->createMock(VisitorManager::class);
        $this->visitorInfoUtil = $this->createMock(VisitorInfoUtil::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // mock user repository
        $this->entityManager
            ->method('getRepository')
            ->willReturn($this->userRepository);

        // create instance of AuthManager
        $this->authManager = new AuthManager(
            $this->logManager,
            $this->cookieUtil,
            $this->sessionUtil,
            $this->errorManager,
            $this->securityUtil,
            $this->userRepository,
            $this->visitorManager,
            $this->visitorInfoUtil,
            $this->entityManager
        );
    }

    /**
     * Test if the isUserLoggedIn method returns true when the session exists
     *
     * @return void
     */
    public function testIsUserLoggedInWhenSessionExists(): void
    {
        $token = 'valid-token';

        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock user repository
        $user = $this->createMock(User::class);
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertTrue($this->authManager->isUserLogedin());
    }

    /**
     * Test if the isUserLoggedIn method returns false when the session does not exist
     *
     * @return void
     */
    public function testIsUserLoggedInWhenSessionDoesNotExist(): void
    {
        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(false);

        // assert output
        $this->assertFalse($this->authManager->isUserLogedin());
    }

    /**
     * Test if the login method sets the login-token session
     *
     * @return void
     */
    public function testGetUserTokenWhenSessionExists(): void
    {
        $token = 'valid-token';

        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock user repository
        $user = $this->createMock(User::class);
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertEquals($token, $this->authManager->getUserToken());
    }

    /**
     * Test if the getUserToken method returns null when the session does not exist
     *
     * @return void
     */
    public function testGetUserTokenWhenSessionDoesNotExist(): void
    {
        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(false);

        // assert output
        $this->assertNull($this->authManager->getUserToken());
    }

    /**
     * Test if the getUsername method returns the correct username
     *
     * @return void
     */
    public function testGetUsername(): void
    {
        $token = 'valid-token';
        $username = 'testuser';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn($username);

        // mock user repository
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertEquals($username, $this->authManager->getUsername($token));
    }

    /**
     * Test if the getUserRole method returns the correct role
     *
     * @return void
     */
    public function testGetUserRole(): void
    {
        $token = 'valid-token';
        $role = 'Admin';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock user repository
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertEquals($role, $this->authManager->getUserRole($token));
    }

    /**
     * Test if the isAdmin method returns true when the user is an admin
     *
     * @return void
     */
    public function testIsAdminWhenUserIsAdmin(): void
    {
        $token = 'valid-token';
        $role = 'Admin';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock user repository
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertTrue($this->authManager->isAdmin());
    }

    /**
     * Test if the isAdmin method returns false when the user is not an admin
     *
     * @return void
     */
    public function testIsAdminWhenUserIsNotAdmin(): void
    {
        $token = 'valid-token';
        $role = 'User';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock session util
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock user repository
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // assert output
        $this->assertFalse($this->authManager->isAdmin());
    }
}
