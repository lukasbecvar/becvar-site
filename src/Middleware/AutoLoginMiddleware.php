<?php

namespace App\Middleware;

use App\Entity\User;
use App\Manager\AuthManager;
use App\Manager\CookieManager;
use App\Manager\SessionManager;

/*
    This middleware check if requird autologin function
*/

class AutoLoginMiddleware
{
    private $authManager;
    private $cookieManager;
    private $sessionManager;

    public function __construct(
        AuthManager $authManager, 
        CookieManager $cookieManager,
        SessionManager $sessionManager
    ) {
        $this->authManager = $authManager;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
    }

    public function onKernelRequest(): void
    {
        // check if cookie set
        if (isset($_COOKIE['login-token-cookie'])) {
            
            $user = new User();
            $user_token = $this->cookieManager->get('login-token-cookie');

            // check if token exist in database
            if ($this->authManager->getUserRepository(['token' => $user_token]) != null) {
                
                // get user data
                $user = $this->authManager->getUserRepository(['token' => $user_token]);

                // autologin user
                $this->authManager->login($user->getUsername(), $user_token, true);
            } else {
                $this->cookieManager->unset('login-token-cookie');
        
                // destory session is cookie token is invalid
                $this->sessionManager->destroySession();
            }
        }
    }
}
