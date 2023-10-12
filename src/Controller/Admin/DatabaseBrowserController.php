<?php

namespace App\Controller\Admin;

use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
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
                'tables' => $this->databaseManager->getTables()
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

                // disable not used components
                'tables' => null,
                'editor_table' => null,
                'new_row_table' => null,

                // table browser data
                'table_name' => $table,
                'table_exist' => $this->databaseManager->isTableExist($table),
                'table_data' => $this->databaseManager->getTableDataByPage($table, $page),
                'table_data_count_all' => $this->databaseManager->countTableDataCount($table),
                'table_data_count' => count($this->databaseManager->getTableDataByPage($table, $page)),
                'table_columns' => $this->databaseManager->getTableColumns($table),
                'limit' => $_ENV['ITEMS_PER_PAGE'],
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
    
    #[Route('/admin/database/edit/{page}/{table}/{id}', name: 'admin_database_edit')]
    public function edit(string $table, int $page, int $id): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            $error_msg = null;

            // escape table name
            $table = $this->securityUtil->escapeString($table);
            
            // get table columns
            $columns = $this->databaseManager->getTableColumns($table);

			// check if user submit edit form
			if (isset($_POST["submitEdit"])) {

                // update values
                foreach($columns as $row) { 

                    // check if form value is empty
                    if (empty($_POST[$row])) {
                        if ($row != 'id') {
                            $error_msg = $row.' is empty';
                            break;
                        }
                    } else {
                        // get value & escape
                        $value = $this->securityUtil->escapeString($_POST[$row]);

                        // update value
                        $this->databaseManager->updateValue($table, $row, $value, $id);
                    }
                }

                // redirect back to browser
                if ($error_msg == null) {
                    return $this->redirectToRoute('admin_database_browser', [
                        'table' => $table,
                        'page' => $page
                    ]);
                }
            }            

            return $this->render('admin/database-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // disable not used components
                'tables' => null,
                'table_name' => null,
                'new_row_table' => null,

                // row editor data
                'editor_table' => $table,
                'editor_id' => $id,
                'editor_field' => $columns,
                'editor_values' => $this->databaseManager->selectRowData($table, $id),
                'editor_page' => $page,
                'error_msg' => $error_msg
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }     
    }

    #[Route('/admin/database/add/{page}/{table}', name: 'admin_database_add')]
    public function add(string $table, int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            $error_msg = null;

            // escape table name
            $table = $this->securityUtil->escapeString($table);

            $columns = $this->databaseManager->getTableColumns($table);

            // check if form submited
            if (isset($_POST['submitSave'])) {

                $columnsBuilder = [];
                $valuesBuilder = [];
            
                // Build columns and values list
                foreach ($columns as $column) {
                    if ($column != 'id') {
                        if (!empty($_POST[$column])) {
                            $columnsBuilder[] = $column;
                            $valuesBuilder[] = $this->securityUtil->escapeString($_POST[$column]);
                        } else {
                            $error_msg = 'value: '.$column.' is empty';
                            break;
                        }
                    }
                }
                
                // execute new row insert
                if ($error_msg == null) {
                    $this->databaseManager->addNew($table, $columnsBuilder, $valuesBuilder);
                }

                // redirect back to browser
                if ($error_msg == null) {
                    return $this->redirectToRoute('admin_database_browser', [
                        'table' => $table,
                        'page' => $page
                    ]);
                }
            }

            return $this->render('admin/database-browser.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // disable not used components
                'tables' => null,
                'table_name' => null,
                'editor_table' => null,

                // new row data
                'new_row_table' => $table,
                'new_row_page' => $page,
                'new_row_columns' => $columns,
                'error_msg' => $error_msg
            ]);

        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/database/delete/{page}/{table}/{id}', name: 'admin_database_delete')]
    public function delete(string $table, int $page, $id, Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // escape table name
            $table = $this->securityUtil->escapeString($table);

            // delete row
            $this->databaseManager->deleteRowFromTable($table, $id);
            
            // check if deleted by log-reader
            if ($request->query->get('referer') == 'log_reader') {
                return $this->redirectToRoute('admin_log_list', [
                        'page' => $page
                ]);              
            }

            return $this->redirectToRoute('admin_database_browser', [
                'table' => $table,
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
