<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AdminController
 *
 * Admin controller provides initialization of the admin site.
 * Controller redirects users to the login or dashboard component.
 *
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * Initialize the admin site.
     *
     * @return Response
     */
    #[Route('/admin', methods: ['GET'], name: 'admin_init')]
    public function admin(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }
}
