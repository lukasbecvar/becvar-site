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

    #[Route('/admin/database', name: 'admin_database_list')]
    public function list(): Response
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

                // tables list data
                'tables' => $this->databaseManager->getTables(),

                // table browser data
                'table_name' => null,
                'table_exist' => null,
                'table_data' => null,
                'table_data_count_all' => null,
                'table_data_count' => null,
                'table_columns' => null,
                'limit' => null,
                'page' => null
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/{table}/{page}', name: 'admin_database_browser')]
    public function tableBrowser(string $table, int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // escape table name
            $table = $this->securityUtil->escapeString($table);

            return $this->render('admin/database-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // tables list data
                'tables' => null,

                // table browser data
                'table_name' => $table,
                'table_exist' => $this->databaseManager->isTableExist($table),
                'table_data' => $this->databaseManager->getTableDataByPage($table, $page),
                'table_data_count_all' => $this->databaseManager->countTableData($table),
                'table_data_count' => count($this->databaseManager->getTableDataByPage($table, $page)),
                'table_columns' => $this->databaseManager->getTableColumns($table),
                'limit' => $_ENV['ITEMS_PER_PAGE'],
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/delete/{page}/{table}/{id}', name: 'admin_database_delete')]
    public function delete(string $table, int $page, $id): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // escape table name
            $table = $this->securityUtil->escapeString($table);

            // delete row
            $this->databaseManager->deleteRowFromTable($table, $id);

            return $this->redirectToRoute('admin_database_browser', [
                'table' => $table,
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
