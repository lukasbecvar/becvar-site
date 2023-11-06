<?php

namespace App\Controller\Api;

use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: update user status to online
*/

class UserApiController extends AbstractController
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private ErrorManager $errorManager;

    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        ErrorManager $errorManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    #[Route('/api/user/update/activity', name: 'api_user_status')]
    public function updateStatus(EntityManagerInterface $entityManager)
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get user token
            $token = $this->authManager->getUserToken();

            // get user repository
            $user = $this->authManager->getUserRepository(['token' => $token]);

            // check if user found
            if ($user != null) {
                
                // update users status
                $user->setStatusUpdateTime(time());
                $user->setStatus('online');
    
                // update user status
                try {
                    $entityManager->flush();
                } catch (\Exception $e) {
                    $this->logManager->log('system-error', 'error to update user status: '.$e->getMessage());
                    return $this->json([
                        'status' => 'error',
                        'message' => 'error to update user status'
                    ], 500);
                }

                return $this->json([
                    'status' => 'success',
                    'message' => 'user status updated'
                ], 200);
            } else {
                return $this->json([
                    'status' => 'error',
                    'message' => 'user not found'
                ], 500);
            }
            
        } else {
            $this->errorManager->handleError('error to set online status for non authentificated users!', 401);
        }
    }
}
