<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Manager\CookieManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Dashboard controller provides homepage of admin site
*/

class DashboardController extends AbstractController
{
    private $authManager;
    private $cookieManager;

    public function __construct(AuthManager $authManager, CookieManager $cookieManager)
    {
        $this->authManager = $authManager;
        $this->cookieManager = $cookieManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/dashboard.html.twig');
        } else {
            return $this->redirectToRoute('login');
        }
    }
}
