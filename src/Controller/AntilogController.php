<?php

namespace App\Controller;

use App\Manager\LogManager;
use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Antilog controller provides a function to block database logs.
 * Antilog for admin users disables logging via browser cookie.
 */
class AntilogController extends AbstractController
{
    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /**
     * AntilogController constructor.
     *
     * @param LogManager  $logManager
     * @param AuthManager $authManager
     */
    public function __construct(
        LogManager $logManager,
        AuthManager $authManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
    }

    /**
     * Sets or unsets antilog for admin users.
     *
     * @return Response The response, redirects to the admin dashboard.
     */
    #[Route('/antilog/5369362536', methods: ['GET'], name: 'antilog')]
    public function antilog(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get logged username
            $username = $this->authManager->getUsername();

            // check if user have set antilog
            if (isset($_COOKIE['anti-log-cookie'])) {
                $this->logManager->unsetAntiLogCookie();
                $this->logManager->log('anti-log', 'user: '.$username.' set antilog');
            } else {
                $this->logManager->setAntiLogCookie();
                $this->logManager->log('anti-log', 'user: '.$username.' unset antilog');
            }
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'error to set anti-log for non authentificated users!'
            ], 401);
        }
        return $this->redirectToRoute('admin_dashboard');
    }
}
