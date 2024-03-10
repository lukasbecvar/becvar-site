<?php

namespace App\Controller\Admin\Auth;

use App\Service\Manager\AuthManager;
use App\Service\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LogoutController
 * 
 * Logout controller provides user logout function.
 * Note: Login uses its own authenticator, not Symfony auth.
 * 
 * @package App\Controller\Admin\Auth
 */
class LogoutController extends AbstractController
{
    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;

    /**
     * @var ErrorManager
     * Instance of the ErrorManager for handling error-related functionality.
     */
    private ErrorManager $errorManager;

    /**
     * LogoutController constructor.
     *
     * @param AuthManager  $authManager
     * @param ErrorManager $errorManager
     */
    public function __construct(AuthManager $authManager, ErrorManager $errorManager) 
    {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Handles user logout.
     *
     * @return Response
     */
    #[Route('/logout', methods: ['GET'], name: 'auth_logout')]
    public function logout(): Response
    {
        if ($this->authManager->isUserLogedin()) {
            $this->authManager->logout();
        }

        // verify if user logout
        if (!$this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('auth_login');
        } else {
            return $this->errorManager->handleError('logout error: unknown error in logout function', 500);
        }
    }
}
