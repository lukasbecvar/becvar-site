<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Manager\AuthManager;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Media browser controller provides an image-uploader browser.
 */
class MediaBrowserController extends AbstractController
{
    /** * @var SiteUtil */
    private SiteUtil $siteUtil;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var DatabaseManager */
    private DatabaseManager $databaseManager;

    /**
     * MediaBrowserController constructor.
     *
     * @param SiteUtil        $siteUtil
     * @param AuthManager     $authManager
     * @param DatabaseManager $databaseManager
     */
    public function __construct(
        SiteUtil $siteUtil, 
        AuthManager $authManager, 
        DatabaseManager $databaseManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Display the media browser with image-uploader.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/media/browser', name: 'admin_media_browser')]
    public function mediaBrowser(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
     
            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get media list
            $media = $this->databaseManager->getImages($page);

            return $this->render('admin/media-browser.html.twig', [
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
