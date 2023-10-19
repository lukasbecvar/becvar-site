<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\MessagesManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Inbox controller provides contact form message reader/ban/close messages
*/

class InboxController extends AbstractController
{
    private $authManager;
    private $messagesManager;
    private $visitorInfoUtil;

    public function __construct(
        AuthManager $authManager,
        MessagesManager $messagesManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->authManager = $authManager;
        $this->messagesManager = $messagesManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }
    
    #[Route('/admin/inbox/{page}', name: 'admin_inbox')]
    public function inbox(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get messages data
            $messages = $this->messagesManager->getMessages('open', $page);

            return $this->render('admin/inbox.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // inbox data
                'page' => $page,
                'inbox_data' => $messages, 
                'message_count' => count($messages),
                'message_limit' => $_ENV['ITEMS_PER_PAGE']
            ]);

        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/inbox/close/{id}/{page}', name: 'admin_inbox_close')]
    public function close(int $page, int $id): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // close message
            $this->messagesManager->closeMessage($id);

            // redirect back to inbox
            return $this->redirectToRoute('admin_inbox', [
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
