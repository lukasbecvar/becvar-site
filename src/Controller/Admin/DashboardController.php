<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use App\Util\SiteUtil;
use App\Entity\Message;
use App\Entity\Visitor;
use App\Util\DashboardUtil;
use App\Manager\LogManager;
use App\Manager\BanManager;
use App\Manager\AuthManager;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DashboardController
 *
 * Dashboard controller provides the homepage of the admin site
 * Dashboard components: warning box, services controller, host info, server/database counters
 *
 * @package App\Controller\Admin
 */
class DashboardController extends AbstractController
{
    private SiteUtil $siteUtil;
    private BanManager $banManager;
    private LogManager $logManager;
    private AuthManager $authManager;
    private DashboardUtil $dashboardUtil;
    private VisitorManager $visitorManager;

    public function __construct(
        SiteUtil $siteUtil,
        BanManager $banManager,
        LogManager $logManager,
        AuthManager $authManager,
        DashboardUtil $dashboardUtil,
        VisitorManager $visitorManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->dashboardUtil = $dashboardUtil;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Display the admin dashboard
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/admin/dashboard', methods: ['GET'], name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        // return dashboard page view
        return $this->render('admin/dashboard.twig', [
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // warning box data
            'isSsl' => $this->siteUtil->isSsl(),
            'isDevMode' => $this->siteUtil->isDevMode(),
            'isMaintenance' => $this->siteUtil->isMaintenance(),
            'antiLogEnabled' => $this->logManager->isEnabledAntiLog(),
            'isBrowserListExist' => $this->dashboardUtil->isBrowserListFound(),

            // cards data
            'banned_visitorsCount' => $this->banManager->getBannedCount(),
            'online_users_count' => count($this->authManager->getOnlineUsersList()),
            'onlinevisitorsCount' => count($this->visitorManager->getOnlineVisitorIDs()),
            'visitorsCount' => $this->dashboardUtil->getDatabaseEntityCount(new Visitor()),
            'messagesCount' => $this->dashboardUtil->getDatabaseEntityCount(new Message(), ['status' => 'open']),
            'unreadedLogsCount' => $this->dashboardUtil->getDatabaseEntityCount(new Log(), ['status' => 'unreaded'])
        ]);
    }
}
