<?php

namespace App\Manager;

use App\Entity\User;
use App\Helper\LogHelper;
use App\Helper\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban manager provides login/logout methods
*/

class AuthManager
{
    private $logHelper;
    private $errorHelper;
    private $entityManager;
    private $cookieManager;
    private $sessionManager;

    public function __construct(
        LogHelper $logHelper, 
        ErrorHelper $errorHelper, 
        CookieManager $cookieManager,
        SessionManager $sessionManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
    }

    public function getUserRepository(array $array): ?object 
    {
        // get user repository
        $userRepository = $this->entityManager->getRepository(User::class);

        // try to find user in database
        try {
            $result = $userRepository->findOneBy($array);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        // return result
        if ($result !== null) {
            return $result;
        } else {
            return null;
        }
    }

    public function setLastLoginDate(): void 
    {
        // get current date
        $date = date('d.m.Y H:i:s');

        // user repository
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {

            // update values
            $user->setLastLoginTime($date);

            // try to flush updated data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
            }
        }     
    }

    public function getUserToken(): ?string 
    {
        // default token value
        $token = null;

        // init user entity
        $userEntity = new User();

        // check if session exist
        if ($this->sessionManager->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionManager->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token], $userEntity) != null) {
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

    public function isUserLogedin(): bool 
    {
        // default state
        $state = false;

        // init user entity
        $userEntity = new User();

        // check if session exist
        if ($this->sessionManager->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionManager->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token], $userEntity) != null) {
                $state = true;
            }
        }

        return $state;
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
                        $this->cookieManager->set("login-token-cookie", $user_token, time() + (60*60*24*7*365));
                    }
                }

                // update last login time
                $this->setLastLoginDate();

                // log to mysql
                $this->logHelper->log('authenticator', 'user: '.$username.' logged in');

            } else {
                $this->errorHelper->handleError('error to login user with token: '.$user_token, 500);
            }
        }
    }

    public function logout(): void 
    {
        // check if user logged in
        if ($this->isUserLogedin()) {
            // init user entity
            $user = $this->getUserRepository(['token' => $this->getUserToken()]);

            // unset user-token cookie
            $this->cookieManager->unset("login-token-cookie");
        
            // log action to mysql
            $this->logHelper->log('authenticator', 'user: '.$user->getUsername().' logout');
        
            // destroy all sessions
            $this->sessionManager->destroySession();   
        } 
    }

    public function isUsersEmpty(): bool
    {
        // get user repos
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
}
