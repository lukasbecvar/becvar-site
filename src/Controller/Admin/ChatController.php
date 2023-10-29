<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Chat controller provides admin chat box
*/

class ChatController extends AbstractController
{
    private AuthManager $authManager;
    private VisitorManager $visitorManager;

    public function __construct(
        AuthManager $authManager,
        VisitorManager $visitorManager
    ) {
        $this->authManager = $authManager;
        $this->visitorManager = $visitorManager;
    }
    
    #[Route('/admin/chat', name: 'admin_chat')]
    public function chat(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            return $this->render('admin/chat.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // chat data
                'online_users' => $this->authManager->getUsersWhereStatus('online')
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
