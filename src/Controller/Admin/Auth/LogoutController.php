<?php

namespace App\Controller\Admin\Auth;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LogoutController
 *
 * Logout controller provides user logout functionality
 * Note: Login uses its own authenticator (not Symfony security)
 *
 * @package App\Controller\Admin\Auth
 */
class LogoutController extends AbstractController
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Handle user logout
     *
     * @return Response The redirect to login page
     */
    #[Route('/logout', methods: ['GET'], name: 'auth_logout')]
    public function logout(): Response
    {
        // check if user loggedin
        if ($this->authManager->isUserLogedin()) {
            $this->authManager->logout();
        }

        // redirect to login page
        return $this->redirectToRoute('auth_login');
    }
}
