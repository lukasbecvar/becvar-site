<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban manager provides login/logout methods
*/

class AuthManager
{
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private CookieManager $cookieManager;
    private SessionManager $sessionManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LogManager $logManager, 
        ErrorManager $errorManager, 
        CookieManager $cookieManager,
        SessionManager $sessionManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
    }

    public function isUserLogedin(): bool 
    {
        // check if session exist
        if ($this->sessionManager->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionManager->getSessionValue('login-token');

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
                $this->sessionManager->setSession('login-token', $user_token);

                // check if remember set
                if ($remember) {
                    if (!isset($_COOKIE['login-token-cookie'])) {
                        $this->cookieManager->set('login-token-cookie', $user_token, time() + (60*60*24*7*365));
                    }
                }

                // update last login time
                $this->setLastLoginDate();

                // log to mysql
                $this->logManager->log('authenticator', 'user: '.$username.' logged in');

            } else {
                $this->errorManager->handleError('error to login user with token: '.$user_token, 500);
            }
        }
    }

    public function updateUsersStatus(): void
    {
        // timeout (seconds)
        $session_timeout_seconds = 60;

        // get current timestamp
        $current_time = time();
        
        // get users repository
        $userRepository = $this->entityManager->getRepository(User::class);
            
        // check if users found
        if ($userRepository !== null) {
                
            // get users list
            $users = $userRepository->findAll();

            // update all offline statuses
            foreach ($users as $user) {

                // get timestamp
                $last_activity_timestamp = $user->getStatusUpdateTime();

                // update only online users
                if ($user->getStatus() === 'online') {
                    if ($current_time - intval($last_activity_timestamp) >= $session_timeout_seconds) {
                        $user->setStatus('offline');
                    }
                }
            }
        
            // update users status
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update users status: '.$e->getMessage(), 500);
            }
        }
    }

    public function logout(): void 
    {
        // check if user logged in
        if ($this->isUserLogedin()) {
            // init user
            $user = $this->getUserRepository(['token' => $this->getUserToken()]);

            // update user status
            $this->setStatus($user, 'offline');

            $this->logManager->log('authenticator', 'user: '.$user->getUsername().' logout');
            $this->cookieManager->unset('login-token-cookie');
            $this->sessionManager->destroySession();   
        } 
    }

    public function setStatus(object $repo, string $status): void 
    {
        try {
            // set offline status
            $repo->setStatus($status);

            // update database
            $this->entityManager->flush();

        } catch (\Exception $e) {
            $this->errorManager->handleError('error to update user status: '.$e->getMessage(), 500);
        }       
    }

    public function getUsersWhereStatus(string $status): ?array
    {
        return $this->entityManager->getRepository(User::class)->findBy(['status' => $status]);
    }

    public function setLastLoginDate(): void 
    {
        $date = date('d.m.Y H:i:s');
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {

            $user->setLastLoginTime($date);

            // update last login time
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
            }
        }     
    }

    public function getUserToken(): ?string 
    {
        // default token value
        $token = null;

        // check if session exist
        if ($this->sessionManager->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionManager->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token]) != null) {
                $token = $login_token;
            }
        }

        return $token;
    }

    public function getUsername(): ?string 
    {
        $username = null;

        // user repository
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {
            $username = $user->getUsername();
        } 

        return $username;
    }

    public function getUserRole(string $token = 'self'): ?string 
    {
        // get token
        if ($token == 'self') {
            $token = ['token' => $this->getUserToken()];
        }

        // user repository
        $user = $this->getUserRepository($token);

        // check if user repo found
        if ($user != null) {
            return $user->getRole();
        }
    }

    public function getUserProfilePic(string $token = 'self'): ?string 
    {
        $avatar = null;

        // get token
        if ($token == 'self') {
            $token = ['token' => $this->getUserToken()];
        }

        // user repository
        $user = $this->getUserRepository($token);

        // check if user repo found
        if ($user != null) {
            $avatar = $user->getProfilePic();
        } 

        return $avatar;
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
}
