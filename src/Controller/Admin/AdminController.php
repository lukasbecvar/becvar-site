<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Admin controller provides initialization of the admin site.
 * Controller redirects users to the login or dashboard component.
 */
class AdminController extends AbstractController
{
    /** * @var AuthManager */
    private AuthManager $authManager;

    /**
     * AdminController constructor.
     *
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Initialize the admin site.
     *
     * @return Response
     */
    #[Route('/admin', methods: ['GET'], name: 'admin_init')]
    public function admin(): Response
    {
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
