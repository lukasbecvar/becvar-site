<?php

namespace App\Middleware;

use App\Entity\User;
use App\Manager\AuthManager;
use App\Manager\CookieManager;

/*
    This middleware check if requird autologin function
*/

class AutoLoginMiddleware
{
    private $authManager;
    private $cookieManager;

    public function __construct(AuthManager $authManager, CookieManager $cookieManager) 
    {
        $this->authManager = $authManager;
        $this->cookieManager = $cookieManager;
    }

    public function onKernelRequest(): void
    {
        // check if cookie set
        if (isset($_COOKIE['login-token-cookie'])) {
            
            $user = new User();
            $user_token = $_COOKIE['login-token-cookie'];

            // check if token exist in database
            if ($this->authManager->getUserRepository(['token' => $user_token]) != null) {
                
                // get user data
                $user = $this->authManager->getUserRepository(['token' => $user_token]);

                // autologin user
                $this->authManager->login($user->getUsername(), $user_token, true);
            } else {
                $this->cookieManager->unset('login-token-cookie');
        
                // start session (for destroy xDDDDD)
                $this->authManager->startSession();
                // destroy all sessions
                session_destroy();
            }
        }
    }
}
