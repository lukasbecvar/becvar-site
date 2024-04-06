<?php

namespace App\Service\Middleware;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Util\SessionUtil;
use App\Service\Manager\AuthManager;

/**
 * Class AutoLoginMiddleware
 *
 * This middleware checks if the required auto-login function should be triggered.
 * 
 * @package App\Service\Middleware
 */
class AutoLoginMiddleware
{
    private CookieUtil $cookieUtil;
    private SessionUtil $sessionUtil;
    private AuthManager $authManager;

    public function __construct(CookieUtil $cookieUtil, SessionUtil $sessionUtil, AuthManager $authManager) {
        $this->cookieUtil = $cookieUtil;
        $this->sessionUtil = $sessionUtil;
        $this->authManager = $authManager;
    }

    /**
     * Check if auto-login should be performed.
     */
    public function onKernelRequest(): void
    {
        // check if user not logged
        if (!$this->authManager->isUserLogedin()) {
            // check if cookie set
            if (isset($_COOKIE['login-token-cookie'])) {
                
                // init user entity
                $user = new User();

                // get user token
                $user_token = $this->cookieUtil->get('login-token-cookie');

                // check if token exist in database
                if ($this->authManager->getUserRepository(['token' => $user_token]) != null) {
                    
                    // get user data
                    $user = $this->authManager->getUserRepository(['token' => $user_token]);

                    // autologin user
                    $this->authManager->login($user->getUsername(), $user_token, true);
                } else {
                    $this->cookieUtil->unset('login-token-cookie');
            
                    // destory session is cookie token is invalid
                    $this->sessionUtil->destroySession();
                }
            }
        }
    }
}
