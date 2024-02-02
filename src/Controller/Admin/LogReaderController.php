<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LogReaderController
 * 
 * Log reader controller provides read logs from the database table.
 * 
 * @package App\Controller\Admin
 */
class LogReaderController extends AbstractController
{
    /**
     * @var SiteUtil
     * Instance of the SiteUtil for handling site-related utilities.
     */
    private SiteUtil $siteUtil;

    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;

    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var DatabaseManager
     * Instance of the DatabaseManager for handling database-related functionality.
     */
    private DatabaseManager $databaseManager;

    /**
     * LogReaderController constructor.
     *
     * @param SiteUtil        $siteUtil
     * @param LogManager      $logManager
     * @param AuthManager     $authManager
     * @param SecurityUtil    $securityUtil
     * @param DatabaseManager $databaseManager
     */
    public function __construct(
        SiteUtil $siteUtil,
        LogManager $logManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        DatabaseManager $databaseManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Display logs from the database table.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/logs', methods: ['GET'], name: 'admin_log_list')]
    public function logsTable(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get logs data
            $logs = $this->logManager->getLogs('unreaded', $this->authManager->getUsername(), $page);

            return $this->render('admin/log-reader.html.twig', [
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

    /**
     * Display logs filtered by IP address.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/logs/whereip', methods: ['GET'], name: 'admin_log_list_where_ip')]
    public function logsWhereIp(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {

            // get query parameters
            $ip_address = $this->siteUtil->getQueryString('ip', $request);
            $page = intval($this->siteUtil->getQueryString('page', $request));

            // get & escape ip
            $ip_address = $this->securityUtil->escapeString($ip_address);

            // get logs data
            $logs = $this->logManager->getLogsWhereIP($ip_address, $this->authManager->getUsername(), $page);

            return $this->render('admin/log-reader.html.twig', [
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

    /**
     * Display a confirmation page for deleting all logs.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/logs/delete', methods: ['GET'], name: 'admin_log_delete')]
    public function deleteAllLogs(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            $page = intval($this->siteUtil->getQueryString('page', $request));

            return $this->render('admin/elements/confirmation/delete-logs-html.twig', [
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

    /**
     * Set all logs as read.
     *
     * @return Response
     */
    #[Route('/admin/logs/readed/all', methods: ['GET'], name: 'admin_log_readed')]
    public function setReadedAllLogs(): Response
    {
        if ($this->authManager->isUserLogedin()) {
            $this->logManager->setReaded();
            
            return $this->redirectToRoute('admin_dashboard');    
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 
}
