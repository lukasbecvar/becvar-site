<?php

namespace App\Manager;

use Exception;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthManager
 *
 * DatabaseManager provides CRUD database methods
 *
 * @package App\Manager
 */
class DatabaseManager
{
    private LogManager $logManager;
    private Connection $connection;
    private AuthManager $authManager;
    private ErrorManager $errorManager;

    public function __construct(
        LogManager $logManager,
        Connection $connection,
        AuthManager $authManager,
        ErrorManager $errorManager,
    ) {
        $this->logManager = $logManager;
        $this->connection = $connection;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Retrieves a list of tables in the database
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error get tables list
     *
     * @return array<string> The list of tables if successful, otherwise null
     */
    public function getTables(): ?array
    {
        $tablesList = [];
        $tables = null;

        try {
            $platform = $this->connection->getDatabasePlatform();
            $sql = $platform->getListTablesSQL();
            $tables = $this->connection->executeQuery($sql)->fetchAllAssociative();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to get tables list: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // log to database
        $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database list');

        // build tables list
        foreach ($tables as $value) {
            array_push($tablesList, $value['Tables_in_' . $_ENV['DATABASE_NAME']]);
        }

        return $tablesList;
    }

    /**
     * Retrieves the columns of a specific table
     *
     * @param string $tableName The name of the table
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error to get table columns
     *
     * @return array<string> The list of column names
     */
    public function getTableColumns(string $tableName): array
    {
        $table = null;
        $columns = [];
        $schema = $this->connection->createSchemaManager()->introspectSchema();

        // get data
        try {
            $table = $schema->getTable($tableName);
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to get columns from table: ' . $tableName . ', ' . $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }

        return $columns;
    }

    /**
     * Retrieves the columns of a specific database table
     *
     * @param string $tableName  The name of the table for which columns should be retrieved
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error get table data
     *
     * @return array<mixed> The array of column names if successful
     */
    public function getTableData(string $tableName, bool $log = true): array
    {
        $data = [];

        // escape name from sql query
        $tableName = $this->connection->quoteIdentifier($tableName);

        // get data
        try {
            $data = $this->connection->executeQuery('SELECT * FROM ' . $tableName)->fetchAllAssociative();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to get data from table: ' . $tableName . ', ' . $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }

        // log to database
        if ($log) {
            $this->logManager->log(
                name: 'database',
                value: $this->authManager->getUsername() . ' viewed database table: ' . $tableName
            );
        }

        return $data;
    }

    /**
     * Retrieves data from a specific database table with pagination
     *
     * @param string $tableName The name of the table from which to retrieve data
     * @param int $page The page number for pagination (default is 1)
     * @param bool $log Indicates whether to log the action (default is true)
     * @param bool $raw Whether to return raw data without decryption. Default is false
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error get table data
     *
     * @return array<mixed> The array of data from the specified table
     */
    public function getTableDataByPage(string $tableName, int $page = 1, bool $log = true, bool $raw = false): array
    {
        $data = [];
        $itemsPerPage = $_ENV['ITEMS_PER_PAGE'];

        // escape name from sql query
        $tableName = $this->connection->quoteIdentifier($tableName);

        // calculate the offset based on the page number
        $offset = ($page - 1) * $itemsPerPage;

        // get data with LIMIT and OFFSET
        try {
            $query = 'SELECT * FROM ' . $tableName . ' LIMIT ' . $itemsPerPage . ' OFFSET ' . $offset;
            $data = $this->connection->executeQuery($query)->fetchAllAssociative();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to get data from table: ' . $tableName . ', ' . $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }

        // log to database
        if ($log) {
            $this->logManager->log(
                'database',
                $this->authManager->getUsername() . ' viewed database table: ' . $tableName
            );
        }

        // decrypt database data (specify table names)
        $decryptedTables = ["`inbox_messages`", "`users`"];

        // build new data array (decrypt aes data)
        if (in_array($tableName, $decryptedTables)) {
            $decryptedData = [];
            foreach ($data as $value) {
                $arr = [];
                foreach ($value as $key => $val) {
                    if ($raw == true) {
                        $arr[$key] = $val;
                    } else {
                        $arr[$key] = (
                            $key === 'message' ||
                            $key === 'profile_pic' ||
                            $key === 'password'
                        ) ? '[encrypted-data]' : $val;
                    }
                }
                array_push($decryptedData, $arr);
            }
            return $decryptedData;
        }

        return $data;
    }

    /**
     * Retrieves data from a specific row of a database table
     *
     * @param string $tableName The name of the table from which to retrieve data
     * @param int $id The unique identifier of the row
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error get row data
     *
     * @return array<mixed> The array of data from the specified row
     */
    public function selectRowData(string $tableName, int $id): array
    {
        $data = [];
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from($tableName)
                ->where('id = :id')
                ->setParameter('id', $id);

            $statement = $queryBuilder->execute();
            $data = $statement->fetchAllAssociative();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to get data from table: ' . $tableName . ', ' . $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }
        return $data[0];
    }

    /**
     * Adds a new row to a specific database table
     *
     * @param string $tableName The name of the table to which the new row will be added
     * @param array<string> $columns The array of column names for the new row
     * @param array<mixed> $values The array of values corresponding to the columns for the new row
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error insert new row
     *
     * @return void
     */
    public function addNew(string $tableName, array $columns, array $values): void
    {
        // create placeholders for prepared statement
        $columnPlaceholders = array_fill(0, count($columns), '?');
        $columnList = implode(', ', $columns);
        $columnPlaceholderList = implode(', ', $columnPlaceholders);

        // construct the SQL query
        $sql = "INSERT INTO `$tableName` ($columnList) VALUES ($columnPlaceholderList)";

        try {
            // execute query
            $this->connection->executeQuery($sql, $values);
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error insert new row into: ' . $tableName . ', ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // log row add
        $this->logManager->log(
            'database',
            $this->authManager->getUsername() . ' inserted new row to table: ' . $tableName
        );
    }

    /**
     * Deletes a row from a table
     *
     * @param string $tableName The name of the table
     * @param string $id The ID of the row to delete
     *
     * @return void
     */
    public function deleteRowFromTable(string $tableName, string $id): void
    {
        if ($id == 'all') {
            $sql = 'DELETE FROM ' . $tableName . ' WHERE id=id';
            $this->connection->executeStatement($sql);

            $sqlIndexReset = 'ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1';
            $this->connection->executeStatement($sqlIndexReset);
        } else {
            $sql = 'DELETE FROM ' . $tableName . ' WHERE id = :id';
            $params = ['id' => $id];
            $this->connection->executeStatement($sql, $params);
        }
        $this->logManager->log(
            'database',
            $this->authManager->getUsername() . ' deleted row: ' . $id . ', table: ' . $tableName
        );
    }

    /**
     * Updates a specific value in a row of a database table
     *
     * @param string $tableName The name of the table in which the value will be updated
     * @param string $row The column name for which the value will be updated
     * @param string $value The new value to be set
     * @param int $id The unique identifier of the row
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error update value
     *
     * @return void
     */
    public function updateValue(string $tableName, string $row, string $value, int $id): void
    {
        // query builder
        $query = "UPDATE $tableName SET $row = :value WHERE id = :id";

        try {
            $this->connection->executeStatement($query, [
                'value' => $value,
                'id' => $id
            ]);
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'error to update value: ' . $value . ' in: ' . $tableName . ' id: ' . $id . ', error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        $this->logManager->log(
            'database',
            $this->authManager->getUsername() . ': edited ' . $row . ' -> ' . $value . ', in table: ' . $tableName
        );
    }

    /**
     * Counts the total number of rows in a table
     *
     * @param string $tableName The name of the table
     *
     * @return int The total number of rows
     */
    public function countTableData(string $tableName): int
    {
        return count($this->getTableData($tableName, false));
    }

    /**
     * Counts the number of rows on a specific page of a table
     *
     * @param string $tableName The name of the table
     * @param int $page The page number
     *
     * @return int The number of rows on the page
     */
    public function countTableDataByPage(string $tableName, int $page): int
    {
        return count($this->getTableDataByPage($tableName, $page, false));
    }
}
