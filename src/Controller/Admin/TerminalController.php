<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Terminal controller provides admin server shell
*/

class TerminalController extends AbstractController
{
    private AuthManager $authManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(AuthManager $authManager, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->authManager = $authManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/terminal', name: 'admin_terminal')]
    public function admin(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/terminal.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
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
