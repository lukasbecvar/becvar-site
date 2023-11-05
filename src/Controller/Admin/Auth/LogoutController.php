<?php

namespace App\Controller\Admin\Auth;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Logout controller provides user logout function
*/

class LogoutController extends AbstractController
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;

    public function __construct(AuthManager $authManager, ErrorManager $errorManager)
    {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    #[Route('/logout', name: 'auth_logout')]
    public function logout()
    {
        // logout user (if session found)
        if ($this->authManager->isUserLogedin()) {
            $this->authManager->logout();
        }

        // check if logged out
        if (!$this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('auth_login');
        } else {
            $this->errorManager->handleError('logout error: unknown error in logout function', 500);
        }
    }
}
