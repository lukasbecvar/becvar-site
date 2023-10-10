<?php

namespace App\Controller;

use App\Helper\ErrorHelper;
use App\Helper\LogHelper;
use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
    Antilog controller provides function for set antilog cookie
*/

class AntilogController extends AbstractController
{
    private $logHelper;
    private $errorHeler;
    private $authManager;

    public function __construct(
        LogHelper $logHelper,
        ErrorHelper $errorHeler, 
        AuthManager $authManager,
    ) {
        $this->logHelper = $logHelper;
        $this->errorHeler = $errorHeler;
        $this->authManager = $authManager;
    }

    #[Route('/antilog/5369362536', name: 'antilog')]
    public function index(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            $username = $this->authManager->getUsername();

            if (isset($_COOKIE['anti-log-cookie'])) {
                $this->logHelper->unsetAntiLogCookie();
                $this->logHelper->log('anti-log', 'user: '.$username.' set antilog');
            } else {
                $this->logHelper->setAntiLogCookie();
                $this->logHelper->log('anti-log', 'user: '.$username.' unset antilog');
            }
        } else {
            $this->errorHeler->handleError('error to set anti-log-cookie for non authentificated users!', 401);
        }
        return $this->redirectToRoute('admin_dashboard');
    }
}
