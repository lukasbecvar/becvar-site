<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Account settings controller provides user account changes (username, password, profile pic)
*/

class AccountSettingsController extends AbstractController
{
    private $authManager;
    private $visitorInfoUtil;

    public function __construct(
        AuthManager $authManager, 
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->authManager = $authManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/account/settings', name: 'admin_account_settings')]
    public function accountSettings(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/account-settings.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
