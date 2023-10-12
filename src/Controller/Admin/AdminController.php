<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Admin controller provides init admin site
*/

class AdminController extends AbstractController
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    #[Route('/admin', name: 'admin_init')]
    public function admin(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
