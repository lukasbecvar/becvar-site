<?php

namespace App\Manager;

use App\Util\SecurityUtil;
use Doctrine\DBAL\Connection;

/**
 * Class AuthManager
 *
 * DatabaseManager provides methods for retrieving, editing, and managing database data when it is not possible to use the entity manager.
 *
 * @package App\Manager
 */
class DatabaseManager
{
    private LogManager $logManager;
    private Connection $connection;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;

    public function __construct(
        LogManager $logManager,
        Connection $connection,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
    ) {
        $this->logManager = $logManager;
        $this->connection = $connection;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Retrieves a list of tables in the database.
     *
     * @throws \Exception If there is an error during the retrieval of the tables list.
     *
     * @return array<string> The list of tables if successful, otherwise null.
     */
    public function getTables(): ?array
    {
        $tables_list = [];
        $tables = null;

        try {
            $platform = $this->connection->getDatabasePlatform();
            $sql = $platform->getListTablesSQL();
            $tables = $this->connection->executeQuery($sql)->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get tables list: ' . $e->getMessage(), 500);
        }

        // build tables list
        foreach ($tables as $value) {
            array_push($tables_list, $value['Tables_in_' . $_ENV['DATABASE_NAME']]);
        }

        $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database list');

        return $tables_list;
    }

    /**
     * Retrieves the columns of a specific table.
     *
     * @param string $table_name The name of the table.
     *
     * @return array<string> The list of column names.
     */
    public function getTableColumns(string $table_name): array
    {
        $table = null;
        $columns = [];
        $schema = $this->connection->createSchemaManager()->introspectSchema();

        // get data
        try {
            $table = $schema->getTable($table_name);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get columns from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }

        return $columns;
    }

    /**
     * Retrieves the columns of a specific database table.
     *
     * @param string $table_name  The name of the table for which columns should be retrieved.
     *
     * @throws \Exception If there is an error during the retrieval of the table columns or the table is not found.
     *
     * @return array<mixed> The array of column names if successful.
     */
    public function getTableData(string $table_name, bool $log = true): array
    {
        $data = [];

        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);

        // get data
        try {
            $data = $this->connection->executeQuery('SELECT * FROM ' . $table_name)->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }

        // log to database
        if ($log) {
            $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
        }

        return $data;
    }

    /**
     * Retrieves data from a specific database table with pagination.
     *
     * @param string $table_name  The name of the table from which to retrieve data.
     * @param int    $page        The page number for pagination (default is 1).
     * @param bool   $log         Indicates whether to log the action (default is true).
     * @param bool   $raw         Whether to return raw data without decryption. Default is false.
     *
     * @throws \Exception If there is an error during the retrieval of the table data or the table is not found.
     *
     * @return array<mixed> The array of data from the specified table.
     */
    public function getTableDataByPage(string $table_name, int $page = 1, bool $log = true, bool $raw = false): array
    {
        $data = [];
        $itemsPerPage = $_ENV['ITEMS_PER_PAGE'];

        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);

        // calculate the offset based on the page number
        $offset = ($page - 1) * $itemsPerPage;

        // get data with LIMIT and OFFSET
        try {
            $query = 'SELECT * FROM ' . $table_name . ' LIMIT ' . $itemsPerPage . ' OFFSET ' . $offset;
            $data = $this->connection->executeQuery($query)->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }

        // log to database
        if ($log) {
            $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
        }

        // decrypt database data (specify table names)
        $decrypted_tables = ["`todos`", "`chat_messages`", "`code_paste`", "`images`", "`inbox_messages`", "`users`"];

        // build new data array (decrypt aes data)
        if (in_array($table_name, $decrypted_tables)) {
            $decrypted_data = [];
            foreach ($data as $value) {
                $arr = [];
                foreach ($value as $key => $val) {
                    if ($raw == true) {
                        $arr[$key] = $val;
                    } else {
                        $arr[$key] = (
                            $key === 'text' ||
                            $key === 'message' ||
                            $key === 'content' ||
                            $key === 'image' ||
                            $key === 'profile_pic' ||
                            $key === 'password'
                        ) ? '[encrypted-data]' : $val;
                    }
                }
                array_push($decrypted_data, $arr);
            }
            return $decrypted_data;
        }

        return $data;
    }

    /**
     * Retrieves data from a specific row of a database table.
     *
     * @param string $table_name  The name of the table from which to retrieve data.
     * @param int    $id          The unique identifier of the row.
     *
     * @throws \Exception If there is an error during the retrieval of the row data or the table is not found.
     *
     * @return array<mixed> The array of data from the specified row.
     */
    public function selectRowData(string $table_name, int $id): array
    {
        $data = [];
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from($table_name)
                ->where('id = :id')
                ->setParameter('id', $id);

            $statement = $queryBuilder->execute();
            $data = $statement->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }
        return $data[0];
    }

    /**
     * Adds a new row to a specific database table.
     *
     * @param string $table_name  The name of the table to which the new row will be added.
     * @param array<string> $columns     The array of column names for the new row.
     * @param array<mixed> $values      The array of values corresponding to the columns for the new row.
     *
     * @throws \Exception If there is an error during the insertion of the new row or the table is not found.
     *
     * @return void
     */
    public function addNew(string $table_name, array $columns, array $values): void
    {
        // create placeholders for prepared statement
        $columnPlaceholders = array_fill(0, count($columns), '?');
        $columnList = implode(', ', $columns);
        $columnPlaceholderList = implode(', ', $columnPlaceholders);

        // construct the SQL query
        $sql = "INSERT INTO `$table_name` ($columnList) VALUES ($columnPlaceholderList)";

        try {
            // execute query
            $this->connection->executeQuery($sql, $values);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error insert new row into: ' . $table_name . ', ' . $e->getMessage(), 500);
        }

        $this->logManager->log('database', $this->authManager->getUsername() . ' inserted new row to table: ' . $table_name);
    }

    /**
     * Deletes a row from a table.
     *
     * @param string $table_name The name of the table.
     * @param string $id The ID of the row to delete.
     *
     * @return void
     */
    public function deleteRowFromTable(string $table_name, string $id): void
    {
        if ($id == 'all') {
            $sql = 'DELETE FROM ' . $table_name . ' WHERE id=id';
            $this->connection->executeStatement($sql);

            $sql_index_reset = 'ALTER TABLE ' . $table_name . ' AUTO_INCREMENT = 1';
            $this->connection->executeStatement($sql_index_reset);
        } else {
            $sql = 'DELETE FROM ' . $table_name . ' WHERE id = :id';
            $params = ['id' => $id];
            $this->connection->executeStatement($sql, $params);
        }
        $this->logManager->log('database', $this->authManager->getUsername() . ' deleted row: ' . $id . ', table: ' . $table_name);
    }

    /**
     * Updates a specific value in a row of a database table.
     *
     * @param string $table_name  The name of the table in which the value will be updated.
     * @param string $row         The column name for which the value will be updated.
     * @param string $value       The new value to be set.
     * @param int    $id          The unique identifier of the row.
     *
     * @throws \Exception If there is an error during the update of the value or the table is not found.
     *
     * @return void
     */
    public function updateValue(string $table_name, string $row, string $value, int $id): void
    {
        // query builder
        $query = "UPDATE $table_name SET $row = :value WHERE id = :id";

        try {
            $this->connection->executeStatement($query, [
                'value' => $value,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to update value: ' . $value . ' in: ' . $table_name . ' id: ' . $id . ', error: ' . $e->getMessage(), 500);
        }
        $this->logManager->log('database', $this->authManager->getUsername() . ': edited ' . $row . ' -> ' . $value . ', in table: ' . $table_name);
    }

    /**
     * Retrieves decrypted images from the 'images' table.
     *
     * @param int $page The page number.
     *
     * @return array<mixed>|null Decrypted images or null if an error occurs.
     */
    public function getImages(int $page): ?array
    {
        $images = [];

        // get images list
        $images_list = $this->getTableDataByPage('images', $page, true, true);

        // get image data (this is for decrypt image)
        foreach ($images_list as $image) {
            // decrypt image data
            $image_data = $this->securityUtil->decryptAes($image['image']);

            // check if image data is decrypted
            if ($image_data == null) {
                $this->errorManager->handleError('Error to decrypt aes image data', 500);
            }

            $image_item = [
                'id' => $image['id'],
                'token' => $image['token'],
                'image' => $image_data
            ];
            array_push($images, $image_item);
        }

        return $images;
    }

    /**
     * Checks if a table exists in the database.
     *
     * @param string $table_name The name of the table.
     *
     * @return bool True if the table exists, false otherwise.
     */
    public function isTableExist(string $table_name): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$table_name]);
    }

    /**
     * Counts the total number of rows in a table.
     *
     * @param string $table_name The name of the table.
     *
     * @return int The total number of rows.
     */
    public function countTableData(string $table_name): int
    {
        return count($this->getTableData($table_name, false));
    }

    /**
     * Counts the number of rows on a specific page of a table.
     *
     * @param string $table_name The name of the table.
     * @param int $page The page number.
     *
     * @return int The number of rows on the page.
     */
    public function countTableDataByPage(string $table_name, int $page): int
    {
        return count($this->getTableDataByPage($table_name, $page, false));
    }
}
