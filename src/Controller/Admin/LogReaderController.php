<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Log reader controller provides read logs from database table
*/

class LogReaderController extends AbstractController
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    #[Route('/admin/logs', name: 'admin_log_list')]
    public function index(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return die('logs');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
