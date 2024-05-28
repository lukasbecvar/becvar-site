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
    private SiteUtil $siteUtil;
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private DatabaseManager $databaseManager;

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
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/logs', methods: ['GET'], name: 'admin_log_list')]
    public function logsTable(Request $request): Response
    {
        // get page
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // get logs data
        $logs = $this->logManager->getLogs('unreaded', $this->authManager->getUsername(), $page);

        // render log reader view
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
    }

    /**
     * Display logs filtered by IP address.
     *
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/logs/whereip', methods: ['GET'], name: 'admin_log_list_where_ip')]
    public function logsWhereIp(Request $request): Response
    {
        // get query parameters
        $ipAddress = $this->siteUtil->getQueryString('ip', $request);
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // get & escape ip
        $ipAddress = $this->securityUtil->escapeString($ipAddress);

        // get logs data
        $logs = $this->logManager->getLogsWhereIP($ipAddress, $this->authManager->getUsername(), $page);

        // render log reader view
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
            'where_ip' => $ipAddress
        ]);
    }

    /**
     * Display a confirmation page for deleting all logs.
     *
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/logs/delete', methods: ['GET'], name: 'admin_log_delete')]
    public function deleteAllLogs(Request $request): Response
    {
        // get page from query string
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // render delete confirmation view
        return $this->render('admin/elements/confirmation/delete-logs-html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // delete confirmation data
            'page' => $page
        ]);
    }

    /**
     * Set all logs as read.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/logs/readed/all', methods: ['GET'], name: 'admin_log_readed')]
    public function setReadedAllLogs(): Response
    {
        // set all logs as readed
        $this->logManager->setReaded();

        // redirect back to dashboard
        return $this->redirectToRoute('admin_dashboard');
    }
}
