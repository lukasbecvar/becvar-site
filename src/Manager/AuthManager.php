<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\Visitor;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Auth manager provides login/logout methods
    ! Login uses its own Authenticator not symfony auth !
*/

class AuthManager
{
    private LogManager $logManager;
    private CookieUtil $cookieUtil;
    private SessionUtil $sessionUtil;
    private ErrorManager $errorManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

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

    public function isAdmin(): bool
    {
        $token = $this->getUserToken();
        $role = $this->getUserRole($token);

        if ($role == 'Owner' || $role == 'Admin') {
            return true;
        } else {
            return false;
        }
    }

    public function isRegisterPageAllowed(): bool
    {
        if ($this->isUsersEmpty() or ($this->isUserLogedin() && $this->isAdmin())) {
            return true;
        } else {
            return false;
        }
    }

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
  