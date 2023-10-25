<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/*
    Ban manager provides login/logout methods
*/

class AuthManager
{
    private $logManager;
    private $errorManager;
    private $entityManager;
    private $cookieManager;
    private $sessionManager;

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
        $userEntity = new User();

        // check if session exist
        if ($this->sessionManager->checkSession('login-token')) {

            // get login token form session
            $login_token = $this->sessionManager->getSessionValue('login-token');

            // check if token exist in database
            if ($this->getUserRepository(['token' => $login_token], $userEntity) != null) {
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

    public function logout(): void 
    {
        // check if user logged in
        if ($this->isUserLogedin()) {
            // init user
            $user = $this->getUserRepository(['token' => $this->getUserToken()]);

            $this->logManager->log('authenticator', 'user: '.$user->getUsername().' logout');
            $this->cookieManager->unset('login-token-cookie');
            $this->sessionManager->destroySession();   
        } 
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

    public function getUserRole(): ?string 
    {
        $role = null;

        // user repository
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user != null) {
            $role = $user->getRole();
        } 

        return strtolower($role);
    }

    public function getUserProfilePic(): ?string 
    {
        $avatar = null;

        // user repository
        $user = $this->getUserRepository(['token' => $this->getUserToken()]);

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
