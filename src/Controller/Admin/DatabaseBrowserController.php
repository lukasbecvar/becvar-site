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
                'table_data' => null,
                'table_to_delete' => null
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
                'table_data' => $this->databaseManager->getTableData($table),
                'table_to_delete' => null
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/delete_all/{table}', name: 'admin_database_table_delete_all')]
    public function databaseTableDeleteAll(string $table): Response
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
                'table_to_delete' => $table
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/delete/{table}/{id}', name: 'admin_database_table_delete')]
    public function databaseTableDelete(string $table, string $id): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get & escape values
            $table = $this->securityUtil->escapeString($table);
            $id = $this->securityUtil->escapeString($id);

            // delete row
            $this->databaseManager->deleteRowFromTable($table, $id);

            return $this->redirectToRoute('admin_database_table', [
                'table' => $table
            ]);
        }
    }
}
