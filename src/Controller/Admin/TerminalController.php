<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class TerminalController
 * 
 * Terminal controller provides an admin server shell.
 * 
 * @package App\Controller\Admin
 */
class TerminalController extends AbstractController
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Display the admin server shell.
     *
     * @return Response
     */
    #[Route('/admin/terminal', methods: ['GET'], name: 'admin_terminal')]
    public function admin(): Response
    {
        return $this->render('admin/terminal.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic()
        ]);
    }
}
