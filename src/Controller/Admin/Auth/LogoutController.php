<?php

namespace App\Controller\Admin\Auth;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Logout controller provides logout function
*/

class LogoutController extends AbstractController
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    #[Route('/logout', name: 'auth_logout')]
    public function logout(): Response
    {
        // logout user (if session found)
        if ($this->authManager->isUserLogedin()) {
            $this->authManager->logout();
        }

        return $this->redirectToRoute('auth_login');
    }
}
