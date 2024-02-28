<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Manager\AuthManager;
use App\Manager\MessagesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class InboxController
 * 
 * Inbox controller provides contact form message reader/ban/close messages.
 * 
 * @package App\Controller\Admin
 */
class InboxController extends AbstractController
{
    /**
     * @var SiteUtil
     * Instance of the SiteUtil for handling site-related utilities.
     */
    private SiteUtil $siteUtil;

    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;

    /**
     * @var MessagesManager
     * Instance of the MessagesManager for handling messages-related functionality.
     */
    private MessagesManager $messagesManager;

    /**
     * InboxController constructor.
     *
     * @param SiteUtil        $siteUtil
     * @param AuthManager     $authManager
     * @param MessagesManager $messagesManager
     */
    public function __construct(
        SiteUtil $siteUtil,
        AuthManager $authManager,
        MessagesManager $messagesManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->messagesManager = $messagesManager;
    }

    /**
     * Display inbox messages.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/inbox', methods: ['GET'], name: 'admin_inbox')]
    public function inbox(Request $request): Response
    {
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // get messages data
        $messages = $this->messagesManager->getMessages('open', $page);

        return $this->render('admin/inbox.html.twig', [
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
    }

    /**
     * Close a message in the inbox.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/inbox/close', methods: ['GET'], name: 'admin_inbox_close')]
    public function close(Request $request): Response
    {
        // get query parameters
        $page = intval($this->siteUtil->getQueryString('page', $request));
        $id = intval($this->siteUtil->getQueryString('id', $request));

        // close message
        $this->messagesManager->closeMessage($id);

        // get messages count
        $messages_count = count($this->messagesManager->getMessages('open', 1));

        // check if messages count is 0
        if ($messages_count == 0) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // redirect back to inbox
        return $this->redirectToRoute('admin_inbox', [
            'page' => $page
        ]);
    }
}
