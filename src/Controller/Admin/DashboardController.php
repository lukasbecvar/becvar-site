<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Dashboard controller provides homepage of admin site
*/

class DashboardController extends AbstractController
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            dd($this->authManager->isUserLogedin());
            return $this->render('admin/dashboard.html.twig');
        } else {
            return $this->redirectToRoute('login');
        }
    }
}
