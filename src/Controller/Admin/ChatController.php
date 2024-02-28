<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ChatController
 * 
 * Chat controller provides an admin chat box.
 * 
 * @package App\Controller\Admin
 */
class ChatController extends AbstractController
{
    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;
    
    /**
     * AuthManager constructor.
     *
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager) 
    {
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
        return $this->render('admin/chat.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // chat data
            'online_users' => $this->authManager->getUsersWhereStatus('online')
        ]);
    }
}
