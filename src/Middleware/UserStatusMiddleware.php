<?php

namespace App\Middleware;

use App\Entity\User;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;

/*
    This middleware updates users online stats
*/

class UserStatusMiddleware
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AuthManager $authManager,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(): void
    {   
        // check if user is online
        if ($this->authManager->isUserLogedin()) {

            // timeout update
            $session_timeout_minutes = 1;

            // get current timestamp
            $current_time = time();

            // get timeout seconds
            $session_timeout_seconds = $session_timeout_minutes * 60; 
        
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

                    if ($current_time - intval($last_activity_timestamp) >= $session_timeout_seconds && $user->getStatus() === 'online') {
                        $user->setStatus('offline');
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
    }
}
