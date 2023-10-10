<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Util\DashboardUtil;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ServiceManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Database browser controller provides database tables browser/editor
*/

class DatabaseBrowserController extends AbstractController
{
    private $siteUtil;
    private $logHelper;
    private $authManager;
    private $securityUtil;
    private $dashboardUtil;
    private $serviceManager;
    private $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        LogHelper $logHelper,
        AuthManager $authManager, 
        SecurityUtil $securityUtil,
        DashboardUtil $dashboardUtil,
        ServiceManager $serviceManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->logHelper = $logHelper;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->dashboardUtil = $dashboardUtil;
        $this->serviceManager = $serviceManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/database', name: 'admin_database_browser')]
    public function dashboard(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            return $this->render('admin/database-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),



            ]);
            die('test');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
