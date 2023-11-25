<?php

namespace App\Manager;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * AuthManager provides login/logout methods.
 * Note: Login uses its own Authenticator, not Symfony auth.
 */
class AuthManager
{
    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var CookieUtil */
    private CookieUtil $cookieUtil;

    /** * @var SessionUtil */
    private SessionUtil $sessionUtil;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /** * @var VisitorManager */
    private VisitorManager $visitorManager;

    /** * @var VisitorInfoUtil */
    private VisitorInfoUtil $visitorInfoUtil;

    /** * @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * AuthManager constructor.
     *
     * @param LogManager             $logManager
     * @param CookieUtil             $cookieUtil
     * @param SessionUtil            $sessionUtil
     * @param ErrorManager           $errorManager
     * @param VisitorManager         $visitorManager
     * @param VisitorInfoUtil        $visitorInfoUtil
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LogManager $logManager, 
        CookieUtil $cookieUtil,
        SessionUtil $sessionUtil,
        ErrorManager $errorManager, 
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->cookieUtil = $cookieUtil;
        $this->logManager = $logManager;
        $this->sessionUtil = $sessionUtil;
        $this->errorManager = $errorManager;
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
                $this->sessionUtil->setSession('login-token', $user_token);

                // check if remember set
                if ($remember) {
                    if (!isset($_COOKIE['login-token-cookie'])) {
                        $this->cookieUtil->set('login-token-cookie', $user_token, time() + (60*60*24*7*365));
                    }
                }

                // update last login time
                $this->updateUserData();

                // log to mysql
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
     * Updates user data.
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

        // get count
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
     * @param array $array The criteria to search for in the repository.
     * @return object|null The user entity or null if not found.
     */
    public function getUserRepository(array $array): ?object 
    {
        $result = null;
        $userRepository = $this->entityManager->getRepository(User::class);

        // try to find user in database
        try {
            $result = $userRepository->findOneBy($array);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        // return result
        if ($result !== null) {
            return $result;
        } else {
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
     * @return array|null An array of users with the specified status or null if not found.
     */
    public function getUsersWhereStatus(string $status): ?array
    {
        // get all users data
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $online_users = [];

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
}
  