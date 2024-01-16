<?php

namespace App\Controller\Admin\Auth;

use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Logout controller provides user logout function.
 * Note: Login uses its own authenticator, not Symfony auth.
 */
class LogoutController extends AbstractController
{
    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var ErrorManager */
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

        if (!$this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('auth_login');
        } else {
            return $this->errorManager->handleError('logout error: unknown error in logout function', 500);
        }
    }
}
