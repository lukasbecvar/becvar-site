<?php

namespace App\Controller\Admin;

use App\Util\AppUtil;
use App\Manager\BanManager;
use App\Manager\AuthManager;
use App\Manager\MessagesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class InboxController
 *
 * Inbox controller provides contact form message reader/ban/close messages
 *
 * @package App\Controller\Admin
 */
class InboxController extends AbstractController
{
    private AppUtil $appUtil;
    private BanManager $banManager;
    private AuthManager $authManager;
    private MessagesManager $messagesManager;

    public function __construct(
        AppUtil $appUtil,
        BanManager $banManager,
        AuthManager $authManager,
        MessagesManager $messagesManager
    ) {
        $this->appUtil = $appUtil;
        $this->banManager = $banManager;
        $this->authManager = $authManager;
        $this->messagesManager = $messagesManager;
    }

    /**
     * Display inbox messages
     *
     * @param Request $request The request object
     *
     * @return Response The inbox page view
     */
    #[Route('/admin/inbox', methods: ['GET'], name: 'admin_inbox')]
    public function inbox(Request $request): Response
    {
        // get page from query string
        $page = intval($this->appUtil->getQueryString('page', $request));

        // get messages data
        $messages = $this->messagesManager->getMessages('open', $page);

        // render inbox view
        return $this->render('admin/inbox.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // ban manager instance
            'banManager' => $this->banManager,

            // inbox data
            'page' => $page,
            'inboxData' => $messages,
            'messageCount' => count($messages),
            'messageLimit' => $_ENV['ITEMS_PER_PAGE']
        ]);
    }

    /**
     * Close a message in the inbox
     *
     * @param Request $request The request object
     *
     * @return Response The redirect back to inbox
     */
    #[Route('/admin/inbox/close', methods: ['GET'], name: 'admin_inbox_close')]
    public function close(Request $request): Response
    {
        // get query parameters
        $page = intval($this->appUtil->getQueryString('page', $request));
        $id = intval($this->appUtil->getQueryString('id', $request));

        // close message
        $this->messagesManager->closeMessage($id);

        // get messages count
        $messagesCount = count($this->messagesManager->getMessages('open', 1));

        // check if messages count is 0
        if ($messagesCount == 0) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // redirect back to inbox
        return $this->redirectToRoute('admin_inbox', [
            'page' => $page
        ]);
    }
}
