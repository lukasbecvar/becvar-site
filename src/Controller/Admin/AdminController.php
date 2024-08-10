<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AdminController
 *
 * Admin controller provides initialization of the admin site
 * Controller redirects loggedin users to dashboard page
 *
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * Initialize the admin site
     *
     * @return Response Redirect to dashboard page
     */
    #[Route('/admin', methods: ['GET'], name: 'admin_init')]
    public function admin(): Response
    {
        // rediret to dashboard page
        return $this->redirectToRoute('admin_dashboard');
    }
}
