<?php

namespace App\Manager;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

/**
 * Class AuthManager
 *
 * AuthManager provides login/logout methods.
 * Note: Login uses its own Authenticator, not Symfony auth.
 *
 * @package App\Manager
 */
class AuthManager
{
    private LogManager $logManager;
    private CookieUtil $cookieUtil;
    private SessionUtil $sessionUtil;
    private EmailManager $emailManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private UserRepository $userRepository;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LogManager $logManager,
        CookieUtil $cookieUtil,
        SessionUtil $sessionUtil,
        EmailManager $emailManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        UserRepository $userRepository,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->cookieUtil = $cookieUtil;
        $this->sessionUtil = $sessionUtil;
        $this->emailManager = $emailManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Checks if a user is logged in.
     *
     * @return bool
     */
    public function isUserLogedin(): bool
    {
        // check if session exist
        if (!$this->sessionUtil->checkSession('login-token')) {
            return false;
        }

        // get login token form session
        $loginToken = $this->sessionUtil->getSessionValue('login-token');

        // check if token exist in database
        if ($this->getUserRepository(['token' => $loginToken]) != null) {
            return true;
        }

        return false;
    }

    /**
     * Logs in a user.
     *
     * @param string $username
     * @param string $userToken
     * @param bool   $remember
     *
     * @return void
     */
    public function login(string $username, string $userToken, bool $remember): void
    {
        // check if user not logged in
        if (!$this->isUserLogedin()) {
            // check if user token is valid
            if (!empty($userToken)) {
                // set login session
                $this->sessionUtil->setSession('login-token', $userToken);

                // check if remember set (autologin cookie)
                if ($remember) {
                    if (!isset($_COOKIE['login-token-cookie'])) {
                        $this->cookieUtil->set('login-token-cookie', $userToken, time() + (60 * 60 * 24 * 7 * 365));
                    }
                }

                // update last login time
                $this->updateUserData();

                // log auth action
                $this->logManager->log('authenticator', 'user: ' . $username . ' logged in');

                // send login email security alert
                if (!$this->logManager->isEnabledAntiLog() && $_ENV['MAILER_ENABLED'] == 'true') {
                    $this->emailManager->sendEmail(
                        $_ENV['CONTACT_EMAIL'],
                        'becvar-site User login alert',
                        'New user login: ' . $username . ' in becvar-site detected!',
                        false
                    );
                }
            } else {
                $this->errorManager->handleError('error to login user with token: ' . $userToken, 500);
            }
        }
    }

    /**
     * Logs out a user.
     *
     * @return void
     */
    public function logout(): void
    {
        // check if user logged in
        if ($this->isUserLogedin()) {
            // init user
            $user = $this->getUserRepository(['token' => $this->getUserToken()]);

            // log logout event
            $this->logManager->log('authenticator', 'user: ' . $user->getUsername() . ' logout');

            // unset login cookie
            $this->cookieUtil->unset('login-token-cookie');

            // unset login session
            $this->sessionUtil->destroySession();
        }
    }

    /**
     * Updates user data
     *
     * @throws \Exception If there is an error during the flushing of the user data.
     *
     * @return void
     */
    public function updateUserData(): void
    {
        // get date & time
        $date = date('d.m.Y H:i:s');

        // get current visitor ip address
        $ipAddress = $this->visitorInfoUtil->getIP();

        // get visitor id
        $visitorId = $this->visitorManager->getVisitorRepository($ipAddress)->getID();

        // get user data
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {
            // update last login time
            $user->setLastLoginTime($date);

            // update visitor id
            $user->setVisitorId($visitorId);

            // update user data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: ' . $e->getMessage(), 500);
            }
        }
    }

    /**
     * Registers a new user.
     *
     * @param string $username The username for the new user.
     * @param string $password The password for the new user.
     *
     * @throws \Exception If there is an error during the registration process.
     *
     * @return void
     */
    public function registerNewUser(string $username, string $password): void
    {
        // init user enity
        $user = new User();

        // get current date
        $date = date('d.m.Y H:i:s');

        // get user ip
        $ipAddress = $this->visitorInfoUtil->getIP();

        // generate token
        $token = ByteString::fromRandom(32)->toString();

        // get visitor id
        $visitorId = $this->visitorManager->getVisitorID($ipAddress);

        // password hash
        $hashedPassword = $this->securityUtil->genBcryptHash($password, 10);

        // default profile pics base64
        $imageBase64 = 'non-pic';

        // set user entity data
        $user->setUsername($username);
        $user->setPassword($hashedPassword);
        $user->setRole('Owner');
        $user->setIpAddress($ipAddress);
        $user->setToken($token);
        $user->setRegistedTime($date);
        $user->setLastLoginTime('not logged');
        $user->setProfilePic($imageBase64);
        $user->setVisitorId(strval($visitorId));

        // insert new user
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // log registration event
            $this->logManager->log('authenticator', 'registration new user: ' . $username . ' registred');
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to register new user: ' . $e->getMessage(), 400);
        }

        // set user token (login-token session)
        if (!$this->isUserLogedin()) {
            $this->login($username, $user->getToken(), false);
        }
    }

    /**
     * Retrieves the login token for the current user session.
     *
     * @return string|null The login token or null if not found or invalid.
     */
    public function getUserToken(): ?string
    {
        // check if session exist
        if (!$this->sessionUtil->checkSession('login-token')) {
            return null;
        }

        // get login token form session
        $loginToken = $this->sessionUtil->getSessionValue('login-token');

        // check if token exist in database
        if ($this->getUserRepository(['token' => $loginToken]) != null) {
            return $loginToken;
        }

        return null;
    }

    /**
     * Retrieves the username associated with the given token.
     *
     * @param string $token The user token to retrieve the username for.
     * @return string|null The username or null if not found.
     */
    public function getUsername(string $token = 'self'): ?string
    {
        // get token
        if ($token == 'self') {
            $token = $this->getUserToken();
        }

        // user repository
        $user = $this->getUserRepository(['token' => $token]);

        // check if user repo found
        if ($user != null) {
            return $user->getUsername();
        }

        return null;
    }

    /**
     * Retrieves the role associated with the given token.
     *
     * @param string $token The user token to retrieve the role for.
     * @return string|null The user role or null if not found.
     */
    public function getUserRole(string $token = 'self'): ?string
    {
        // get token
        if ($token == 'self') {
            $token = $this->getUserToken();
        }

        // user repository
        $user = $this->getUserRepository(['token' => $token]);

        // check if user repo found
        if ($user != null) {
            return $user->getRole();
        }

        return null;
    }

    /**
     * Retrieves the profile picture URL associated with the given token.
     *
     * @param string $token The user token to retrieve the profile picture URL for.
     * @return string|null The profile picture URL or null if not found.
     */
    public function getUserProfilePic(string $token = 'self'): ?string
    {
        // get token
        if ($token == 'self') {
            $token = $this->getUserToken();
        }

        // user repository
        $user = $this->getUserRepository(['token' => $token]);

        // check if user repo found
        if ($user != null) {
            $pic = $user->getProfilePic();

            // check if pic is default
            if ($pic == 'non-pic') {
                return base64_encode(file_get_contents('build/images/default-profile.jpg'));
            }

            return $pic;
        }

        return null;
    }

    /**
     * Checks if the user repository is empty.
     *
     * @return bool True if the user repository is empty, false otherwise.
     */
    public function isUsersEmpty(): bool
    {
        $repository = $this->entityManager->getRepository(User::class);

        // get users count
        $count = $repository->createQueryBuilder('p')->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();

        // check if count is zero
        if ($count == 0) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves a user entity from the repository based on the provided criteria.
     *
     * @param array<mixed> $array The criteria to search for in the repository.
     *
     * @throws \Exception If there is an error during the database query.
     *
     * @return object|null The user entity or null if not found.
     */
    public function getUserRepository(array $array): ?object
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        // try to find user in database
        try {
            return $userRepository->findOneBy($array);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: ' . $e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Checks if the user associated with the current session is an administrator.
     *
     * @return bool True if the user is an administrator, false otherwise.
     */
    public function isAdmin(): bool
    {
        $token = $this->getUserToken();
        $role = $this->getUserRole($token);

        // check if user role is admin
        if ($role == 'Owner' || $role == 'Admin') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the registration page is allowed based on the current system state.
     *
     * @return bool True if the registration page is allowed, false otherwise.
     */
    public function isRegisterPageAllowed(): bool
    {
        if ($this->isUsersEmpty() or ($this->isUserLogedin() && $this->isAdmin())) {
            return true;
        }
        return false;
    }

    /**
     * Generate a unique token for a user.
     *
     * @return string The generated user token.
     */
    public function generateUserToken(): string
    {
        // generate user token
        $token = ByteString::fromRandom(32)->toString();

        // get users repository
        $userRepo = $this->entityManager->getRepository(User::class);

        // check if user token is not already used
        if ($userRepo->findOneBy(['token' => $token]) != null) {
            $this->generateUserToken();
        }

        return $token;
    }

    /**
     * Regenerate tokens for all users in the database.
     *
     * This method regenerates tokens for all users in the database, ensuring uniqueness for each token.
     *
     * @return array<bool|null|string> An array containing the status of the operation and any relevant message.
     * - The 'status' key indicates whether the operation was successful (true) or not (false).
     * - The 'message' key contains any relevant error message if the operation failed, otherwise it is null.
     */
    public function regenerateUsersTokens(): array
    {
        $state = [
            'status' => true,
            'message' => null
        ];

        // get all users in database
        $userRepo = $this->entityManager->getRepository(User::class)->findAll();

        // regenerate all users tokens
        foreach ($userRepo as $user) {
            // regenerate new token
            $newToken = $this->generateUserToken();

            // set new token
            $user->setToken($newToken);

            // flush data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $state = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $state;
    }

    /**
     * Retrieves a list of all users along with their associated visitor IDs.
     *
     * This method constructs a query to select usernames, roles, and visitor IDs from the entity represented by this repository.
     * It then executes the query and returns an array containing associative arrays for each user,
     * with keys 'username', 'role', and 'visitor_id' representing the respective user details.
     *
     * @return array<array<string>> An array containing details of all users along with their associated visitor IDs.
     *               Each element of the array is an associative array with keys 'username', 'role', and 'visitor_id',
     *               representing the username, role, and associated visitor ID respectively.
     */
    public function getOnlineUsersList(): array
    {
        $onlineVisitors = [];

        // get all users list
        $users = $this->userRepository->getAllUsersWithVisitorId();

        foreach ($users as $user) {
            // get visitor status
            $status = $this->visitorManager->getVisitorStatus(intval($user['visitor_id']));

            // check visitor status
            if ($status == 'online') {
                array_push($onlineVisitors, $user);
            }
        }

        return $onlineVisitors;
    }
}
