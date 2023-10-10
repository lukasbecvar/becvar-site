<?php

namespace App\Controller\Admin;

use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Database browser controller provides database tables browser/editor
*/

class DatabaseBrowserController extends AbstractController
{
    private $authManager;
    private $securityUtil;
    private $databaseManager;
    private $visitorInfoUtil;

    public function __construct(
        AuthManager $authManager, 
        SecurityUtil $securityUtil,
        DatabaseManager $databaseManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->databaseManager = $databaseManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/database', name: 'admin_database_browser')]
    public function databaseList(): Response
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

                // database browser data
                'tables' => $this->databaseManager->getTables(),
                'table_data' => null
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/{table}', name: 'admin_database_table')]
    public function databaseTable(string $table): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // get table name & escape
            $table = $this->securityUtil->escapeString($table);

            return $this->render('admin/database-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // database browser data
                'tables' => null,
                'table_name' => $table,
                'rows_count' => $this->databaseManager->countTableData($table),
                'table_exist' => $this->databaseManager->isTableExist($table),
                'table_columns' => $this->databaseManager->getTableColumns($table),
                'table_data' => $this->databaseManager->getTableData($table)
            ]);
            die('test');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
