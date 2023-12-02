<?php

namespace App\Manager;

use App\Util\SecurityUtil;
use Doctrine\DBAL\Connection;

/**
 * DatabaseManager provides methods for retrieving, editing, and managing database data when it is not possible to use the entity manager.
 */
class DatabaseManager
{
    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var Connection */
    private Connection $connection;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;
        
    /**
     * DatabaseManager constructor.
     *
     * @param LogManager   $logManager
     * @param Connection   $connection
     * @param AuthManager  $authManager
     * @param SecurityUtil $securityUtil
     * @param ErrorManager $errorManager
     */
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
     * @return array|null A list of table names or null if an error occurs.
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
            $this->errorManager->handleError('error to get tables list: '.$e->getMessage(), 500);
        }

        // build tables list
        foreach ($tables as $value) {
            array_push($tables_list, $value['Tables_in_'.$_ENV['DATABASE_NAME']]);
        }

        // log to database
        $this->logManager->log('database', $this->authManager->getUsername().' viewed database list');

        return $tables_list;
    }

    /**
     * Retrieves the columns of a specific table.
     *
     * @param string $table_name The name of the table.
     *
     * @return array The list of column names.
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
            $this->errorManager->handleError('error to get columns from table: '.$table_name.', '.$e->getMessage(), 404);
        }

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }
        
        return $columns;
    }

    /**
     * Retrieves all data from a specific table.
     *
     * @param string $table_name The name of the table.
     * @param bool $log Whether to log the action.
     *
     * @return array The table data.
     */
    public function getTableData(string $table_name, bool $log = true): array
    {
        $data = [];

        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);

        // get data
        try {
            $data = $this->connection->executeQuery('SELECT * FROM '.$table_name)->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        
        // log to database
        if ($log) {
            $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
        }

        return $data;
    }

    /**
     * Retrieves paginated data from a specific table.
     *
     * @param string $table_name The name of the table.
     * @param int $page The page number.
     * @param bool $log Whether to log the action.
     *
     * @return array The paginated table data.
     */
    public function getTableDataByPage(string $table_name, int $page = 1, bool $log = true): array
    {
        $data = [];
        $itemsPerPage = $_ENV['ITEMS_PER_PAGE'];
    
        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);
    
        // Calculate the offset based on the page number
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
    
        return $data;
    }

    /**
     * Retrieves the data of a specific row in a table.
     *
     * @param string $table_name The name of the table.
     * @param int $id The ID of the row.
     *
     * @return array The row data.
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
            $this->errorManager->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        return $data[0];
    }

    /**
     * Adds a new row to a table.
     *
     * @param string $table_name The name of the table.
     * @param array $columns The column names.
     * @param array $values The values to insert.
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
    
        // execute the prepared statement
        try {
            $this->connection->executeQuery($sql, $values); 
        } catch (\Exception $e) {
            $this->errorManager->handleError('error insert new row into: '.$table_name.', '.$e->getMessage(), 500);
        }

        $this->logManager->log('database', $this->authManager->getUsername(). ' inserted new row to table: '.$table_name);     
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
            $sql = 'DELETE FROM '.$table_name.' WHERE id=id';
            $this->connection->executeStatement($sql);

            $sql_index_reset = 'ALTER TABLE '.$table_name.' AUTO_INCREMENT = 1';
            $this->connection->executeStatement($sql_index_reset);
        } else {
            $sql = 'DELETE FROM '.$table_name.' WHERE id = :id';
            $params = ['id' => $id];
            $this->connection->executeStatement($sql, $params);
        }
    
        // log to database
        $this->logManager->log('database', $this->authManager->getUsername().' deleted row: '.$id.', table: '.$table_name);
    }

    /**
     * Updates a value in a specific row of a table.
     *
     * @param string $table_name The name of the table.
     * @param string $row The column to update.
     * @param string $value The new value.
     * @param int $id The ID of the row.
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
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to update value: '.$value.' in: '.$table_name.' id: '.$id.', error: '.$e->getMessage(), 500);
        }
        $this->logManager->log('database', $this->authManager->getUsername().': edited '.$row.' -> '.$value.', in table: '.$table_name);
    }

    /**
     * Retrieves decrypted images from the 'images' table.
     *
     * @param int $page The page number.
     *
     * @return array|null Decrypted images or null if an error occurs.
     */
    public function getImages(int $page): ?array
    {
        $images_list = $this->getTableDataByPage('images', $page);

        $images = [];

        foreach ($images_list as $image) {
            $image_item = [
                'id' => $image['id'],
                'token' => $image['token'],
                'image' => $this->securityUtil->decryptAes($image['image'])
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
