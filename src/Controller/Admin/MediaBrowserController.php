<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Media browser controller provides image-uploader browser
*/

class MediaBrowserController extends AbstractController
{
    private AuthManager $authManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private DatabaseManager $databaseManager;

    public function __construct(
        AuthManager $authManager, 
        VisitorInfoUtil $visitorInfoUtil,
        DatabaseManager $databaseManager
    ) {
        $this->authManager = $authManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->databaseManager = $databaseManager;
    }

    #[Route('/admin/media/browser/{page}', name: 'admin_media_browser')]
    public function mediaBrowser(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
     
            $media = $this->databaseManager->getImages($page);

            return $this->render('admin/media-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // media browser data
                'page' => $page,
                'media_data' => $media, 
                'media_count' => count($media),
                'media_limit' => $_ENV['ITEMS_PER_PAGE']
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
