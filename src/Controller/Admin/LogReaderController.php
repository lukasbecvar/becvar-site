<?php

namespace App\Controller\Admin;

use App\Util\AppUtil;
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
 * Log reader controller provides read logs from the database table
 *
 * @package App\Controller\Admin
 */
class LogReaderController extends AbstractController
{
    private AppUtil $appUtil;
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private DatabaseManager $databaseManager;

    public function __construct(
        AppUtil $appUtil,
        LogManager $logManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        DatabaseManager $databaseManager
    ) {
        $this->appUtil = $appUtil;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Display logs from the database table
     *
     * @param Request $request The request object
     *
     * @return Response The log reader page view
     */
    #[Route('/admin/logs', methods: ['GET'], name: 'admin_log_list')]
    public function logsTable(Request $request): Response
    {
        // get page
        $page = intval($this->appUtil->getQueryString('page', $request));

        // get logs data
        $logs = $this->logManager->getLogs('unreaded', $this->authManager->getUsername(), $page);

        // render log reader view
        return $this->render('admin/log-reader.twig', [
            // log reader data
            'whereIp' => null,
            'logsData' => $logs,
            'readerPage' => $page,
            'logsCount' => count($logs),
            'limitValue' => $_ENV['ITEMS_PER_PAGE'],
            'loginLogsCount' => $this->logManager->getLoginLogsCount(),
            'unreeadedCount' => $this->logManager->getLogsCount('unreaded'),
            'logsAllCount' => $this->databaseManager->countTableData('logs'),
            'visitorData' => $this->databaseManager->getTableData('visitors', false)
        ]);
    }

    /**
     * Display logs filtered by IP address
     *
     * @param Request $request The request object
     *
     * @return Response The log reader page view (filtered by IP)
     */
    #[Route('/admin/logs/whereip', methods: ['GET'], name: 'admin_log_list_whereIp')]
    public function logsWhereIp(Request $request): Response
    {
        // get query parameters
        $ipAddress = $this->appUtil->getQueryString('ip', $request);
        $page = intval($this->appUtil->getQueryString('page', $request));

        // get & escape ip
        $ipAddress = $this->securityUtil->escapeString($ipAddress);

        // get logs data
        $logs = $this->logManager->getLogsWhereIP($ipAddress, $this->authManager->getUsername(), $page);

        // render log reader view
        return $this->render('admin/log-reader.twig', [
            // log reader data
            'logsData' => $logs,
            'readerPage' => $page,
            'whereIp' => $ipAddress,
            'logsCount' => count($logs),
            'limitValue' => $_ENV['ITEMS_PER_PAGE'],
            'loginLogsCount' => $this->logManager->getLoginLogsCount(),
            'unreeadedCount' => $this->logManager->getLogsCount('unreaded'),
            'logsAllCount' => $this->databaseManager->countTableData('logs'),
            'visitorData' => $this->databaseManager->getTableData('visitors', false)
        ]);
    }

    /**
     * Display a confirmation page for deleting all logs
     *
     * @param Request $request The request object
     *
     * @return Response The delete confirmation page view
     */
    #[Route('/admin/logs/delete', methods: ['GET'], name: 'admin_log_delete')]
    public function deleteAllLogs(Request $request): Response
    {
        // get page from query string
        $page = intval($this->appUtil->getQueryString('page', $request));

        // render delete confirmation view
        return $this->render('admin/elements/confirmation/delete-logs-html.twig', [
            // delete confirmation data
            'page' => $page
        ]);
    }

    /**
     * Set all logs as read
     *
     * @return Response The redirect back to dashboard
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
