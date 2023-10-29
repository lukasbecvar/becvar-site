<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Manager\AuthManager;
use App\Manager\MessagesManager;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Inbox controller provides contact form message reader/ban/close messages
*/

class InboxController extends AbstractController
{
    private SiteUtil $siteUtil;
    private AuthManager $authManager;
    private MessagesManager $messagesManager;
    private VisitorManager $visitorManager;

    public function __construct(
        SiteUtil $siteUtil,
        AuthManager $authManager,
        MessagesManager $messagesManager,
        VisitorManager $visitorManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->messagesManager = $messagesManager;
        $this->visitorManager = $visitorManager;
    }
    
    #[Route('/admin/inbox', name: 'admin_inbox')]
    public function inbox(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get messages data
            $messages = $this->messagesManager->getMessages('open', $page);

            return $this->render('admin/inbox.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
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

    #[Route('/admin/inbox/close', name: 'admin_inbox_close')]
    public function close(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get query parameters
            $page = intval($this->siteUtil->getQueryString('page', $request));
            $id = intval($this->siteUtil->getQueryString('id', $request));

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
