<?php

namespace App\Controller\Api;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: update user status to online
*/

class UserStatusController extends AbstractController
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;

    public function __construct(
        AuthManager $authManager,
        ErrorManager $errorManager
    ) {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    #[Route('/api/user/activity/iwvtqakxzkldtemmicanf', name: 'user_status')]
    public function updateStatus(EntityManagerInterface $entityManager): Response
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
                $entityManager->flush();
    
                return $this->json(['status' => 'success']);
            } else {

                return $this->json(['status' => 'error'], 404);
            }
            
        } else {
            $this->errorManager->handleError('error to set online status for non authentificated users!', 401);
            return new RedirectResponse('/');
        }
    }
}
