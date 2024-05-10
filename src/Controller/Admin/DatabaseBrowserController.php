<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Manager\AuthManager;
use App\Manager\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DatabaseBrowserController
 *
 * Database browser controller provides a database tables browser/editor.
 * Database browser components: table list, table view, edit row, insert row, update projects list.
 *
 * @package App\Controller\Admin
 */
class DatabaseBrowserController extends AbstractController
{
    private SiteUtil $siteUtil;
    private AuthManager $authManager;
    private DatabaseManager $databaseManager;

    public function __construct(SiteUtil $siteUtil, AuthManager $authManager, DatabaseManager $databaseManager)
    {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Display the list of database tables.
     *
     * @return Response
     */
    #[Route('/admin/database', methods: ['GET'], name: 'admin_database_list')]
    public function databaseList(): Response
    {
        return $this->render('admin/database-browser.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // tables list data
            'tables' => $this->databaseManager->getTables()
        ]);
    }

    /**
     * Display the view of a specific database table.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/database/table', methods: ['GET'], name: 'admin_database_browser')]
    public function tableView(Request $request): Response
    {
        // get query parameters
        $table = $this->siteUtil->getQueryString('table', $request);
        $page = intval($this->siteUtil->getQueryString('page', $request));

        return $this->render('admin/database-browser.html.twig', [
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
            'table_data_count_all' => $this->databaseManager->countTableData($table),
            'table_data_count' => $this->databaseManager->countTableDataByPage($table, $page),
            'table_columns' => $this->databaseManager->getTableColumns($table),
            'limit' => $_ENV['ITEMS_PER_PAGE'],
            'page' => $page
        ]);
    }

    /**
     * Edit a specific row in a database table.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/database/edit', methods: ['GET', 'POST'], name: 'admin_database_edit')]
    public function rowEdit(Request $request): Response
    {
        // init default resources
        $errorMsg = null;

        // get query parameters
        $page = intval($this->siteUtil->getQueryString('page', $request));
        $id = intval($this->siteUtil->getQueryString('id', $request));
        $table = $this->siteUtil->getQueryString('table', $request);

        // get table columns
        $columns = $this->databaseManager->getTableColumns($table);

        // get referer query string
        $referer = $request->query->get('referer');

        // check request is post
        if ($request->isMethod('POST')) {
            // get form submit status
            $formSubmit = $request->request->get('submitEdit');

            // check if user submit edit form
            if (isset($formSubmit)) {
                // update values
                foreach ($columns as $row) {
                    // check if form value is empty
                    if (empty($_POST[$row])) {
                        if ($row != 'id') {
                            $errorMsg = $row . ' is empty';
                            break;
                        }
                    } else {
                        // get value
                        $value = $request->request->get($row);

                        // update value
                        $this->databaseManager->updateValue($table, $row, $value, $id);
                    }
                }

                // redirect back to browser
                if ($errorMsg == null) {
                    return $this->redirectToRoute('admin_database_browser', [
                        'table' => $table,
                        'page' => $page
                    ]);
                }
            }
        }

        return $this->render('admin/database-browser.html.twig', [
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
            'editor_referer' => $referer,
            'error_msg' => $errorMsg
        ]);
    }

    /**
     * Add a new row to a database table.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/database/add', methods: ['GET', 'POST'], name: 'admin_database_add')]
    public function rowAdd(Request $request): Response
    {
        // init default resources
        $errorMsg = null;

        // get query parameters
        $table = $this->siteUtil->getQueryString('table', $request);
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // get table columns
        $columns = $this->databaseManager->getTableColumns($table);

        // check request is post
        if ($request->isMethod('POST')) {
            // get form submit status
            $formSubmit = $request->request->get('submitSave');

            // check if form submited
            if (isset($formSubmit)) {
                $columnsBuilder = [];
                $valuesBuilder = [];

                // build columns and values list
                foreach ($columns as $column) {
                    if ($column != 'id') {
                        $columnValue = $request->request->get($column);
                        if (!empty($columnValue)) {
                            $columnsBuilder[] = $column;
                            $valuesBuilder[] = $columnValue;
                        } else {
                            $errorMsg = 'value: ' . $column . ' is empty';
                            break;
                        }
                    }
                }

                // execute new row insert
                if ($errorMsg == null) {
                    $this->databaseManager->addNew($table, $columnsBuilder, $valuesBuilder);
                }

                // redirect back to browser
                if ($errorMsg == null) {
                    return $this->redirectToRoute('admin_database_browser', [
                        'table' => $table,
                        'page' => $page
                    ]);
                }
            }
        }

        return $this->render('admin/database-browser.html.twig', [
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
            'error_msg' => $errorMsg
        ]);
    }

    /**
     * Delete a row from a database table.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/database/delete', methods: ['GET'], name: 'admin_database_delete')]
    public function rowDelete(Request $request): Response
    {
        // get query parameters
        $page = intval($this->siteUtil->getQueryString('page', $request));
        $id = $this->siteUtil->getQueryString('id', $request);
        $table = $this->siteUtil->getQueryString('table', $request);

        // delete row
        $this->databaseManager->deleteRowFromTable($table, $id);

        // check if deleted by log-reader
        if ($request->query->get('referer') == 'log_reader') {
            return $this->redirectToRoute('admin_log_list', [
                'page' => $page
            ]);
        }

        // check if deleted by visitors-manager
        if ($request->query->get('referer') == 'visitor_manager') {
            return $this->redirectToRoute('admin_visitor_manager', [
                'page' => $page
            ]);
        }

        // check if deleted by media-browser
        if ($request->query->get('referer') == 'media_browser') {
            return $this->redirectToRoute('admin_media_browser', [
                'page' => $page
            ]);
        }

        // check if deleted by todo-manager
        if ($request->query->get('referer') == 'todo_manager') {
            return $this->redirectToRoute('admin_todos_completed');
        }

        return $this->redirectToRoute('admin_database_browser', [
            'table' => $table,
            'page' => $page
        ]);
    }
}
