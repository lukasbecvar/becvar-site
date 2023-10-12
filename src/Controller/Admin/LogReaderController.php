<?php

namespace App\Controller\Admin;

use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\BanManager;
use App\Util\VisitorInfoUtil;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Log reader controller provides read logs from database table
*/

class LogReaderController extends AbstractController
{
    private $logManager;
    private $authManager;
    private $visitorInfoUtil;
    private $databaseManager;

    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        VisitorInfoUtil $visitorInfoUtil,
        DatabaseManager $databaseManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->databaseManager = $databaseManager;
    }

    #[Route('/admin/logs/{page}', name: 'admin_log_list')]
    public function view(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

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
                'logs_all_count' => $this->databaseManager->countTableData('log'),
                'unreeaded_count' => $this->logManager->getLogsCount('unreaded'),
                'login_logs_count' => $this->logManager->getLoginLogsCount(),
                'visitor_data' => $this->databaseManager->getTableData('visitor', false),
                'limit_value' => $_ENV['ITEMS_PER_PAGE']
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/logs/delete/{page}', name: 'admin_log_delete')]
    public function delete(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
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
    public function setReaded(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            $this->logManager->setReaded();

            return $this->redirectToRoute('admin_dashboard');    
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 
}
