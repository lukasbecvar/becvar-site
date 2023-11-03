<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Terminal controller provides admin server shell
*/

class TerminalController extends AbstractController
{
    private AuthManager $authManager;
    private VisitorManager $visitorManager;

    public function __construct(AuthManager $authManager, VisitorManager $visitorManager)
    {
        $this->authManager = $authManager;
        $this->visitorManager = $visitorManager;
    }

    #[Route('/admin/terminal', name: 'admin_terminal')]
    public function admin(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/terminal.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic()
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
