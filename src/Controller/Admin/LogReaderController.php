<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Log reader controller provides read logs from database table
*/

class LogReaderController extends AbstractController
{
    private SiteUtil $siteUtil;
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private VisitorInfoUtil $visitorInfoUtil;
    private DatabaseManager $databaseManager;

    public function __construct(
        SiteUtil $siteUtil,
        LogManager $logManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        VisitorInfoUtil $visitorInfoUtil,
        DatabaseManager $databaseManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->databaseManager = $databaseManager;
    }

    #[Route('/admin/logs', name: 'admin_log_list')]
    public function logsTable(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get logs
            $logs = $this->logManager->getLogs('unreaded', $this->authManager->getUsername(), $page);

            return $this->render('admin/log-reader.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // log reader data
                'reader_page' => $page,
                'reader_limit' => $_ENV['ITEMS_PER_PAGE'],
                'logs_data' => $logs,
                'logs_count' => count($logs),
                'logs_all_count' => $this->databaseManager->countTableData('logs'),
                'unreeaded_count' => $this->logManager->getLogsCount('unreaded'),
                'login_logs_count' => $this->logManager->getLoginLogsCount(),
                'visitor_data' => $this->databaseManager->getTableData('visitors', false),
                'limit_value' => $_ENV['ITEMS_PER_PAGE'],
                'where_ip' => null
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/logs/whereip', name: 'admin_log_list_where_ip')]
    public function logsWhereIp(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get ip address
            $ip_address = $this->siteUtil->getQueryString('ip', $request);

            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get & escape ip
            $ip_address = $this->securityUtil->escapeString($ip_address);

            // get logs
            $logs = $this->logManager->getLogsWhereIP($ip_address, $this->authManager->getUsername(), $page);

            return $this->render('admin/log-reader.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // log reader data
                'reader_page' => $page,
                'reader_limit' => $_ENV['ITEMS_PER_PAGE'],
                'logs_data' => $logs,
                'logs_count' => count($logs),
                'logs_all_count' => $this->databaseManager->countTableData('logs'),
                'unreeaded_count' => $this->logManager->getLogsCount('unreaded'),
                'login_logs_count' => $this->logManager->getLoginLogsCount(),
                'visitor_data' => $this->databaseManager->getTableData('visitors', false),
                'limit_value' => $_ENV['ITEMS_PER_PAGE'],
                'where_ip' => $ip_address
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/logs/delete', name: 'admin_log_delete')]
    public function deleteAllLogs(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            return $this->render('admin/elements/confirmation/delete-logs-html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,
    
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),
    
                // delete confirmation data
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 

    #[Route('/admin/logs/readed/all', name: 'admin_log_readed')]
    public function setReadedAllLogs(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // set readed all logs
            $this->logManager->setReaded();

            return $this->redirectToRoute('admin_dashboard');    
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 
}
