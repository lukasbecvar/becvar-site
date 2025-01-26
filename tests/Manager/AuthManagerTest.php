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
 * Test cases for auth manager component
 *
 * @package App\Tests\Manager
 */
class AuthManagerTest extends TestCase
{
    private AuthManager $authManager;
    private LogManager & MockObject $logManager;
    private CookieUtil & MockObject $cookieUtil;
    private SessionUtil & MockObject $sessionUtil;
    private ErrorManager & MockObject $errorManager;
    private SecurityUtil & MockObject $securityUtil;
    private UserRepository & MockObject $userRepository;
    private VisitorManager & MockObject $visitorManager;
    private VisitorInfoUtil & MockObject $visitorInfoUtil;
    private EntityManagerInterface & MockObject $entityManager;

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

        // create auth manager instance
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
     * Test check if user is logged in when session is not available
     *
     * @return void
     */
    public function testCheckIsUserIsLoggedInWhenSessionIsNotAvailable(): void
    {
        // mock session check
        $this->sessionUtil->expects($this->once())->method('checkSession')
            ->with('login-token')->willReturn(false);

        // call tested method
        $result = $this->authManager->isUserLogedin();

        // assert result
        $this->assertFalse($result);
    }

    /**
     * Test check if user is logged in when token is invalid
     *
     * @return void
     */
    public function testCheckIsUserIsLoggedInWhenTokenIsInvalid(): void
    {
        // mock session check
        $this->sessionUtil->expects($this->once())->method('checkSession')
            ->with('login-token')->willReturn(true);

        // mock get session value
        $this->sessionUtil->expects($this->once())->method('getSessionValue')
            ->with('login-token')->willReturn('invalid-token');

        // mock get user by token
        $this->userRepository->expects($this->once())->method('getUserByToken')
            ->with('invalid-token')->willReturn(null);

        // expect destroy session
        $this->sessionUtil->expects($this->once())->method('destroySession');

        // call tested method
        $result = $this->authManager->isUserLogedin();

        // assert result
        $this->assertFalse($result);
    }

    /**
     * Test check if user is logged in when token is valid
     *
     * @return void
     */
    public function testCheckIsUserIsLoggedInWhenTokenIsValid(): void
    {
        // mock session check
        $this->sessionUtil->expects($this->once())->method('checkSession')
            ->with('login-token')->willReturn(true);

        // mock get session value
        $this->sessionUtil->expects($this->once())->method('getSessionValue')
            ->with('login-token')->willReturn('valid-token');

        // mock get user by token
        $userMock = $this->createMock(User::class);
        $this->userRepository->expects($this->once())->method('getUserByToken')
            ->with('valid-token')->willReturn($userMock);

        // call tested method
        $result = $this->authManager->isUserLogedin();

        // assert result
        $this->assertTrue($result);
    }

    /**
     * Test set user login session and cookie
     *
     * @return void
     */
    public function testUserLogin(): void
    {
        $remember = true;
        $username = 'testUser';
        $userToken = 'testToken';

        // mock check login session
        $this->sessionUtil->expects($this->exactly(2))->method('checkSession')
            ->with('login-token')->willReturnOnConsecutiveCalls(false, true);

        // mock login token from session
        $this->sessionUtil->expects($this->any())->method('getSessionValue')
            ->with('login-token')->willReturn($userToken);

        // mock get user by token
        $this->userRepository->expects($this->once())->method('getUserByToken')
            ->with($userToken)->willReturn(new User());

        // mock set login session
        $this->sessionUtil->expects($this->once())->method('setSession')
            ->with('login-token', $userToken);

        // expect set cookie
        $this->cookieUtil->expects($this->once())->method('set')->with(
            $this->equalTo('login-token-cookie'),
            $this->equalTo($userToken),
            $this->greaterThan(time())
        );

        // expect log login event
        $this->logManager->expects($this->once())->method('log')->with(
            name: $this->equalTo('authenticator'),
            value: $this->stringContains('testUser logged in')
        );

        // expect get visitor info
        $this->visitorInfoUtil->expects($this->once())->method('getIP')->willReturn('127.0.0.1');
        $this->visitorManager->expects($this->once())->method('getVisitorRepository')->willReturn(new class {
            public function getID(): int
            {
                return 1;
            }
        });
        $this->userRepository->expects($this->once())->method('findOneBy')
            ->with(['token' => $userToken])->willReturn(new User());

        // expect flush updated user data to database
        $this->entityManager->expects($this->once())->method('flush');

        // call tested method
        $this->authManager->login($username, $userToken, $remember);
    }

    /**
     * Test user logout with session and cookie unset
     *
     * @return void
     */
    public function testUserLogout(): void
    {
        // mock logged-in user entity
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn('test_user');

        // mock session check to simulate logged-in user
        $this->sessionUtil->expects($this->exactly(2))->method('checkSession')
            ->with('login-token')->willReturn(true);
        $this->sessionUtil->expects($this->exactly(2))->method('getSessionValue')
            ->with('login-token')->willReturn('valid-token');

        // mock user repository to return user by token
        $this->userRepository->expects($this->exactly(3))->method('getUserByToken')
            ->with('valid-token')->willReturn($user);

        // mock log manager to verify logout event is logged
        $this->logManager->expects($this->once())->method('log')
            ->with('authenticator', 'user: test_user logout');

        // mock cookie util to verify cookie is unset
        $this->cookieUtil->expects($this->once())->method('unset')
            ->with('login-token-cookie');

        // mock session util to verify session is unset
        $this->sessionUtil->expects($this->once())->method('destroySession');

        // call tested method
        $this->authManager->logout();
    }

    /**
     * Test register new user
     *
     * @return void
     */
    public function testRegisterNewUser(): void
    {
        // mock user data
        $username = 'testuser';
        $password = 'testpassword';
        $hashedPassword = 'hashedpassword';
        $ipAddress = '127.0.0.1';
        $visitorId = 123;

        // mock visitor info data
        $this->visitorInfoUtil->expects($this->exactly(2))->method('getIP')->willReturn('127.0.0.1');

        // mock get visitor repository
        $this->visitorManager->expects($this->once())->method('getVisitorRepository')->willReturn(new class {
            public function getID(): int
            {
                return 1;
            }
        });

        // mock get visitor id
        $this->visitorManager->expects($this->once())->method('getVisitorID')
            ->with($ipAddress)->willReturn($visitorId);

        // mock password hash
        $this->securityUtil->expects($this->once())->method('generateHash')
            ->with($password)->willReturn($hashedPassword);

        // expect findOneBy for check if user exists
        $this->userRepository->expects($this->once())
            ->method('findOneBy')->willReturn(null);

        // expect entity persist
        $this->entityManager->expects($this->once())->method('persist')
            ->with($this->isInstanceOf(User::class));

        // expect flush data with entity manager
        $this->entityManager->expects($this->once())->method('flush');

        // expect event log call (2x because log registration and login)
        $this->logManager->expects($this->exactly(2))->method('log');

        // call tested method
        $this->authManager->registerNewUser($username, $password);
    }

    /**
     * Test get user token when session exists
     *
     * @return void
     */
    public function testGetUserTokenWhenSessionExists(): void
    {
        $token = 'valid-token';

        // mock session
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock get user by token
        $user = $this->createMock(User::class);
        $this->userRepository->method('getUserByToken')->with($token)->willReturn($user);

        // call tested method
        $result = $this->authManager->getUserToken();

        // assert result
        $this->assertEquals($token, $result);
    }

    /**
     * Test get user token when session does not exist
     *
     * @return void
     */
    public function testGetUserTokenWhenSessionDoesNotExist(): void
    {
        // mock session
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(false);

        // call tested method
        $result = $this->authManager->getUserToken();

        // assert result
        $this->assertNull($result);
    }

    /**
     * Test get username by token
     *
     * @return void
     */
    public function testGetUsernameByToken(): void
    {
        $token = 'valid-token';
        $username = 'testuser';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn($username);

        // mock get user by token
        $this->userRepository->method('getUserByToken')->with($token)->willReturn($user);

        // call tested method
        $result = $this->authManager->getUsername($token);

        // assert result
        $this->assertEquals($username, $result);
    }

    /**
     * Test get user role by token
     *
     * @return void
     */
    public function testGetUserRoleByToken(): void
    {
        $token = 'valid-token';
        $role = 'Admin';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock get user by token
        $this->userRepository->method('getUserByToken')->with($token)->willReturn($user);

        // call tested method
        $result = $this->authManager->getUserRole($token);

        // assert result
        $this->assertEquals($role, $result);
    }

    /**
     * Test check if user is admin when user is admin
     *
     * @return void
     */
    public function testCheckIsUserIsAdminWhenUserIsAdmin(): void
    {
        $token = 'valid-token';
        $role = 'Admin';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock session
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock get user by token
        $this->userRepository->method('getUserByToken')->with($token)->willReturn($user);

        // call tested method
        $result = $this->authManager->isAdmin();

        // assert result
        $this->assertTrue($result);
    }

    /**
     * Test check if user is admin when user is not admin
     *
     * @return void
     */
    public function testCheckIsUserIsAdminWhenUserIsNotAdmin(): void
    {
        $token = 'valid-token';
        $role = 'User';

        // mock user
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn($role);

        // mock session
        $this->sessionUtil->method('checkSession')->with('login-token')->willReturn(true);
        $this->sessionUtil->method('getSessionValue')->with('login-token')->willReturn($token);

        // mock get user by token
        $this->userRepository->method('findOneBy')->with(['token' => $token])->willReturn($user);

        // call tested method
        $result = $this->authManager->isAdmin();

        // assert result
        $this->assertFalse($result);
    }

    /**
     * Test generate user token string
     *
     * @return void
     */
    public function testGenerateUserToken(): void
    {
        // call tested method
        $result = $this->authManager->generateUserToken(38);

        // assert result
        $this->assertIsString($result);
    }

    /**
     * Test get online users list
     *
     * @return void
     */
    public function testGetOnlineList(): void
    {
        // call tested method
        $result = $this->authManager->getOnlineUsersList();

        // assert result
        $this->assertIsArray($result);
    }
}
