<?php

namespace App\Middleware;

use App\Manager\AuthManager;

/*
    This middleware updates users online stats
*/

class UserStatusMiddleware
{
    private AuthManager $authManager;

    public function __construct(
        AuthManager $authManager,
    ) {
        $this->authManager = $authManager;
    }

    public function onKernelRequest(): void
    {   
        // check if user is online
        if ($this->authManager->isUserLogedin()) {

            // update user status
            $this->authManager->updateUsersStatus();
        }
    }
}
