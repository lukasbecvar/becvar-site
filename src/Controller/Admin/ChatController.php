<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Chat controller provides an admin chat box.
 */
class ChatController extends AbstractController
{
    /** * @var AuthManager */
    private AuthManager $authManager;
    
    /**
     * AuthManager constructor.
     *
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager) {
        $this->authManager = $authManager;
    }

    /**
     * Display the admin chat box.
     *
     * @return Response
     */
    #[Route('/admin/chat', methods: ['GET'], name: 'admin_chat')]
    public function chat(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            return $this->render('admin/chat.html.twig', [
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
