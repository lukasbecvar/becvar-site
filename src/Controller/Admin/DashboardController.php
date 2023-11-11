<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use App\Entity\Todo;
use App\Entity\Image;
use App\Entity\Paste;
use App\Util\SiteUtil;
use App\Entity\Message;
use App\Entity\Visitor;
use App\Util\SecurityUtil;
use App\Util\DashboardUtil;
use App\Manager\LogManager;
use App\Manager\BanManager;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ServiceManager;
use App\Manager\VisitorManager;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Dashboard controller provides homepage of admin site
    Dashboard components: warning box, services controller, host info, server/database counters
*/

class DashboardController extends AbstractController
{
    private SiteUtil $siteUtil;
    private BanManager $banManager;
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private DashboardUtil $dashboardUtil;
    private ServiceManager $serviceManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        BanManager $banManager,
        LogManager $logManager,
        AuthManager $authManager, 
        SecurityUtil $securityUtil,
        DashboardUtil $dashboardUtil,
        ServiceManager $serviceManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->banManager = $banManager;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->dashboardUtil = $dashboardUtil;
        $this->serviceManager = $serviceManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/dashboard.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => true,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // warning system
                'is_web_user_sudo' => $this->dashboardUtil->isWebUserSudo(),
                'web_service_username' => $this->dashboardUtil->getWebUsername(),
                'is_ssl' => $this->siteUtil->isSsl(),
                'is_maintenance' => $this->siteUtil->isMaintenance(),   
                'is_dev_mode' => $this->siteUtil->isDevMode(),
                'is_services_list_exist' => $this->serviceManager->isServicesListExist(),
                'is_browser_list_exist' => $this->dashboardUtil->isBrowserListFound(),
                'anti_log_enabled' => $this->logManager->isEnabledAntiLog(),

                // dashboard (services controller)
                'services' => $this->serviceManager->getServices(),
                'is_ufw_installed' => $this->serviceManager->isServiceInstalled('ufw'),
                'is_ufw_running' => $this->serviceManager->isUfwRunning(),
                
                // dashboard data (System info)
                'operating_system' => str_replace('DISTRIB_ID=', '', $this->dashboardUtil->getSoftwareInfo()['distro']['operating_system']),
                'kernal_version' => $this->dashboardUtil->getSoftwareInfo()['distro']['kernal_version'],
                'kernal_arch' => $this->dashboardUtil->getSoftwareInfo()['distro']['kernal_arch'],

                // dashboard data (counters)
                'unreaded_logs_count' => $this->dashboardUtil->getDatabaseEntityCount(new Log, ['status' => 'unreaded']),
                'messages_count' => $this->dashboardUtil->getDatabaseEntityCount(new Message, ['status' => 'open']),
                'todos_count' => $this->dashboardUtil->getDatabaseEntityCount(new Todo, ['status' => 'non-completed']),
                'images_count' => $this->dashboardUtil->getDatabaseEntityCount(new Image),
                'pastest_count' => $this->dashboardUtil->getDatabaseEntityCount(new Paste),
                'visitors_count' => $this->dashboardUtil->getDatabaseEntityCount(new Visitor),
                'online_visitors_count' => count($this->visitorManager->getVisitorsWhereStstus('online')),
                'banned_visitors_count' => $this->banManager->getBannedCount(),
                'online_users_count' => count($this->authManager->getUsersWhereStatus('online')),
                'server_uptime' => $this->dashboardUtil->getHostUptime(),   
                'cpu_usage' => $this->dashboardUtil->getCpuUsage(),   
                'ram_usage' => $this->dashboardUtil->getRamUsage()['used'],   
                'drive_usage' => $this->dashboardUtil->getDriveUsage()
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/dashboard/emergency/shutdown', name: 'admin_emergency_shutdown')]
    public function emergencyShutdown(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // default error msg
            $error_msg = null;

            // generate configmation code
            $confirm_code = ByteString::fromRandom(16)->toString();
    
            // check request is post
            if ($request->isMethod('POST')) {

                // get post data
                $form_submit = $request->request->get('submitShutdown');
                $shutdown_code = $request->request->get('shutdownCode');
                $confirm_code = $request->request->get('confirmCode');

                // check if form submited
                if (isset($form_submit)) {

                    // check if codes submited
                    if (isset($shutdown_code) && isset($confirm_code)) {

                        // escape post data
                        $code_1 = $this->securityUtil->escapeString($shutdown_code);
                        $code_2 = $this->securityUtil->escapeString($confirm_code);
    
                        // check if codes is valid
                        if ($code_1 == $code_2) {
            
                            // execute shutdown
                            $this->serviceManager->runAction('emergency_cnA1OI5jBL', 'shutdown_MEjP9bqXF7');
                        } else {
                            $error_msg = 'confirmation codes is not matched';
                        }
                    } else {
                        $error_msg = 'You must enter all values';
                    }
                }
            }
    
            return $this->render('admin/elements/confirmation/emergency-shutdown.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,
    
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),
    
                // form data
                'error_msg' => $error_msg,
                'confirm_code' => $confirm_code
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 

    #[Route('/admin/dashboard/runner', name: 'admin_service_manager')]
    public function serviceActionRunner(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get query parameters
            $service_name = $this->siteUtil->getQueryString('service', $request);
            $action = $this->siteUtil->getQueryString('action', $request);

            // escape values
            $service_name = $this->securityUtil->escapeString($service_name);
            $action = $this->securityUtil->escapeString($action);

            // check if action is emergency shutdown
            if ($service_name == 'emergency' && $action == 'shutdown') {
                return $this->redirectToRoute('admin_emergency_shutdown'); 
            } else {
                // run normal action
                $this->serviceManager->runAction($service_name, $action);
            }
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
