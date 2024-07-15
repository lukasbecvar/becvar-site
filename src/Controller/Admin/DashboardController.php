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
 * Dashboard controller provides the homepage of the admin site.
 * Dashboard components: warning box, services controller, host info, server/database counters.
 *
 * @package App\Controller\Admin
 */
class DashboardController extends AbstractController
{
    private DashboardUtil $dashboardUtil;
    private SiteUtil $siteUtil;
    private BanManager $banManager;
    private LogManager $logManager;
    private AuthManager $authManager;
    private VisitorManager $visitorManager;

    public function __construct(
        DashboardUtil $dashboardUtil,
        SiteUtil $siteUtil,
        BanManager $banManager,
        LogManager $logManager,
        AuthManager $authManager,
        VisitorManager $visitorManager
    ) {
        $this->dashboardUtil = $dashboardUtil;
        $this->siteUtil = $siteUtil;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->visitorManager = $visitorManager;
    }

    /**
     * Display the admin dashboard.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/dashboard', methods: ['GET'], name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            'is_ssl' => $this->siteUtil->isSsl(),
            'is_maintenance' => $this->siteUtil->isMaintenance(),
            'is_dev_mode' => $this->siteUtil->isDevMode(),
            'anti_log_enabled' => $this->logManager->isEnabledAntiLog(),

            'is_browser_list_exist' => $this->dashboardUtil->isBrowserListFound(),

            'visitors_count' => $this->dashboardUtil->getDatabaseEntityCount(new Visitor()),
            'unreaded_logs_count' => $this->dashboardUtil->getDatabaseEntityCount(new Log(), ['status' => 'unreaded']),
            'messages_count' => $this->dashboardUtil->getDatabaseEntityCount(new Message(), ['status' => 'open']),

            'online_visitors_count' => count($this->visitorManager->getOnlineVisitorIDs()),
            'banned_visitors_count' => $this->banManager->getBannedCount(),
            'online_users_count' => count($this->authManager->getOnlineUsersList()),
        ]);
    }
}
