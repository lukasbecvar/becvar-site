<?php

namespace App\Service\Manager;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

/**
 * Class AuthManager
 * 
 * AuthManager provides login/logout methods.
 * Note: Login uses its own Authenticator, not Symfony auth.
 * 
 * @package App\Service\Manager
 */
class AuthManager
{
    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * @var CookieUtil
     * Instance of the CookieUtil for handling cookie-related functionality.
     */
    private CookieUtil $cookieUtil;

    /**
     * @var SessionUtil
     * Instance of the SessionUtil for handling session-related utilities.
     */
    private SessionUtil $sessionUtil;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var VisitorInfoUtil
     * Instance of the VisitorInfoUtil for handling visitor information-related utilities.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * @var EntityManagerInterface
     * Instance of the EntityManagerInterface for interacting with the database.
     */
    private EntityManagerInterface $entityManager;

    /**
     * AuthManager constructor.
     *
     * @param LogManager               $logManager
     * @param CookieUtil               $cookieUtil
     * @param SessionUtil              $sessionUtil
     * @param ErrorManager             $errorManager
     * @param SecurityUtil             $securityUtil
     * @param VisitorManager           $visitorManager
     * @param VisitorInfoUtil          $visitorInfoUtil
     * @param EntityManagerInterface   $entityManager
     */
    public function __construct(
        LogManager $logManager,
        CookieUtil $cookieUtil,
        SessionUtil $sessionUtil,
        ErrorManager $errorManager, 
        SecurityUtil $securityUtil,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->cookieUtil = $cookieUtil;
        $this->sessionUtil = $sessionUtil;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
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
        if ($this->sessionUtil->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionUtil->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token]) != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Logs in a user.
     *
     * @param string $username
     * @param string $user_token
     * @param bool   $remember
     *
     * @return void
     */
    public function login(string $username, string $user_token, bool $remember): void 
    {
        // check if user not logged in
        if (!$this->isUserLogedin()) {
            // check if user token is valid
            if (!empty($user_token)) {
                // set login session
                $this->sessionUtil->setSession('login-token', $user_token);

                // check if remember set (autologin cookie)
                if ($remember) {
                    if (!isset($_COOKIE['login-token-cookie'])) {
                        $this->cookieUtil->set('login-token-cookie', $user_token, time() + (60*60*24*7*365));
                    }
                }

                // update last login time
                $this->updateUserData();

                // log auth action
                $this->logManager->log('authenticator', 'user: '.$username.' logged in');

            } else {
                $this->errorManager->handleError('error to login user with token: '.$user_token, 500);
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
            $this->logManager->log('authenticator', 'user: '.$user->getUsername().' logout');

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
        $ip_address = $this->visitorInfoUtil->getIP();

        // get visitor id
        $visitor_id = $this->visitorManager->getVisitorRepository($ip_address)->getID();

        // get user data
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {

            // update last login time
            $user->setLastLoginTime($date);

            // update visitor id
            $user->setVisitorId($visitor_id);

            // update user data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
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
        $ip_address = $this->visitorInfoUtil->getIP();
                    
        // generate token
        $token = ByteString::fromRandom(32)->toString();
                        
        // get visitor id
        $visitor_id = $this->visitorManager->getVisitorID($ip_address);

        // password hash
        $hashed_password = $this->securityUtil->genBcryptHash($password, 10);

        // default profile pics base64
        $image_base64 = '
            /9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBw4RDQ0OEA0QDhANDQ0NDw4NDhsNDg0
            OFREWFxcTFRUYICggGBolGxMTITEhJSkrLi4uFx8zODMsNygtLisBCgoKDQ0NDg0NDi
            sZFRkrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrK
            ysrK//AABEIAOYA2wMBIgACEQEDEQH/xAAaAAEAAwEBAQAAAAAAAAAAAAAAAQQFAwIH
            /8QAMhABAQABAQYEBAQGAwAAAAAAAAECEQMEEiFRkSIxQWEFcYGhQnKxwSMyUoLh8DN
            i0f/EABUBAQEAAAAAAAAAAAAAAAAAAAAB/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAA
            wDAQACEQMRAD8A+qAKgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
            AAAAAAAAAAAAAAAAAAIebtMf6p3B7HObXH+qd49ygkQkAAAAAAAAAAAAAAAAAAAEWgj
            LKSa26SKe232/hn1v/jhvG3uV9vSfu5A9Z7TK+eVv1eNEiiNHrHKzytnyqAFnZb5lPP
            xT7r2y2kyxlmul6shY3Ta2Zaa8ulvJBpCEgAAAAAAAAAAAAAAAAK2/bSTCzXnfT10WM
            rpLb6c/oyNpncsrlfX7QHkBQAAAAdN2kueOt05uYDZSr7nteLDn5zlVhAAAAAAAAAAA
            AAAAABX37LTC+9mP+9mau/EbywnvapAAKAAAAAALPw/LxWdcf0aLL3O/wATH31n2aiA
            AAAAAAAAAAAAAADjvW14cdZ53lAVfiF8WP5f3VXrabS5XW3V5UAAAAAAAAdN3v8AEw/
            NGqxpdLrPTmv7nvFytmXPSayoLYAAAAAAAAAAAAACp8Qnhntl+y28bXCZY2X1BkD1tM
            LjdLNHlQAAAAAAAAWdwnjvtjVaRpbnseHHn53z9vZB3SAAAAAAAAAAAAACEgK2/wD/A
            B/3Ys5o7/PB/dGcAAoAAAAAAtfD74svy/u0FD4dj4sr6Sad19BCQAAAAAAAAAAAAAAB
            z281wyn/AFrJbNjHzx0tnS6AgBQAAAAAkBf+Hzw29clpz3fDhwxl8/V1QAAAAAAAAAA
            AAAAAAFLf9l5ZSeXnp0XUWAxha2+52S2XWTW6XlZFVQAAAAWNy2VuUvpOf1eNhsLnek
            nnWls8JjJJ5T7+6D0kAAAAAAAAAAAAAAQCRFrxdrjPxTuDoOGW94T8Wvyjllv2Ppjb9
            gd95vgy+TKd9tvWWUs0klcFAAAAF74deWU95+i4ydhtrjrppz6rOO/T1x7VBdFeb5h1
            s+ce8dvhfxQHUeZlOsv1egAAAAAAAAAU983jTwzz9b09gdNvvWOPL+a9J6fNT2m9Z31
            09pycQC29UaJFAAAAAAAAAAAAB0w2+c8sr8rzjmAvbHfZeWU0955f4W5WMsbrvHDdL/
            Lfsg0hCQAAAAc9vtOHG325fNk2+t875rvxDK+HGS9byU+G9L2BAnhvS9jhvS9lECeG9
            L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0
            vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYF/cNrrjcb54/otMzdLcc5yvPleXV
            poAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=
        ';

        // set user entity data
        $user->setUsername($username);
        $user->setPassword($hashed_password);
        $user->setRole('Owner');
        $user->setIpAddress($ip_address);
        $user->setToken($token);
        $user->setRegistedTime($date);
        $user->setLastLoginTime('not logged');
        $user->setProfilePic($image_base64);
        $user->setVisitorId(strval($visitor_id));
        
        // insert new user
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // log registration event
            $this->logManager->log('authenticator', 'registration new user: '.$username.' registred');

        } catch (\Exception $e) {
            $this->errorManager->handleError('error to register new user: '.$e->getMessage(), 400);
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
        if ($this->sessionUtil->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionUtil->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token]) != null) {
                return $login_token;
            } else {
                return null;
            }
        } else {
            return null;
        }
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
        } else {
            return null;
        }
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
        } else {
            return null;
        }
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
            return $user->getProfilePic();
        } else {
            return null;
        }
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
        } else {
            return false;
        }
    }

    /**
     * Retrieves a user entity from the repository based on the provided criteria.
     *
     * @param array<string> $array The criteria to search for in the repository.
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
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
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
        } else {
            return false;
        }
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
        } else {
            return false;
        }
    }

    /**
     * Retrieves a list of users with the specified online or offline status.
     *
     * @param string $status The status to filter users by (e.g., 'online' or 'offline').
     * @return array<array<string,int|string|null>>|null An array of users with the specified status or null if not found.
     */
    public function getUsersWhereStatus(string $status): ?array
    {
        $online_users = [];

        // get all users data
        $users = $this->entityManager->getRepository(User::class)->findAll();

        // check all users status
        foreach ($users as $user) {
            // get user data
            $id = intval($user->getVisitorId());
            $username = $user->getUsername();
            $role = $user->getRole();

            // check user status
            if ($this->visitorManager->getVisitorStatus($id) == $status) {
                $user_item = [
                    'id' => $id,
                    'username' => $username,
                    'role' => $role
                ];
                array_push($online_users, $user_item);
            }
        }

        return $online_users;
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
        $user_repo = $this->entityManager->getRepository(User::class);

        // check if user token is not already used
        if ($user_repo->findOneBy(['token' => $token]) != null) {
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
        $user_repo = $this->entityManager->getRepository(User::class)->findAll();

        // regenerate all users tokens
        foreach ($user_repo as $user) {

            // regenerate new token
            $new_token = $this->generateUserToken();
            
            // set new token
            $user->setToken($new_token);

            // flush data
            try {
                $this->entityManager->flush();
            } catch(\Exception $e) {
                $state = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $state;
    }
}
