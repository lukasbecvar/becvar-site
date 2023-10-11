<?php

namespace App\Manager;

use App\Helper\ErrorHelper;
use App\Helper\LogHelper;
use Doctrine\DBAL\Connection;

/*
    Database manager provides methods for get/edit database data
*/

class DatabaseManager
{
    private $logHelper;
    private $connection;
    private $errorHelper;
    private $authManager;
        
    public function __construct(
        LogHelper $logHelper,
        Connection $connection,
        ErrorHelper $errorHelper,
        AuthManager $authManager
    ) {
        $this->logHelper = $logHelper;
        $this->connection = $connection;
        $this->errorHelper = $errorHelper;
        $this->authManager = $authManager;
    }

    public function getTables(): ?array
    {
        $tables_list = [];

        try {
            $platform = $this->connection->getDatabasePlatform();
            $sql = $platform->getListTablesSQL();
            $tables = $this->connection->executeQuery($sql)->fetchAll();   
        } catch (\Exception $e) {
            $this->errorHelper->handleError('error to get tables list: '.$e->getMessage(), 500);
        }

        // build tables list
        foreach ($tables as $value) {
            array_push($tables_list, $value['Tables_in_'.$_ENV['DATABASE_NAME']]);
        }

        // log to database
        $this->logHelper->log('database-browser', $this->authManager->getUsername().' viewed database list');

        return $tables_list;
    }

    public function isTableExist(string $table_name): bool 
    {
        return $this->connection->getSchemaManager()->tablesExist([$table_name]);
    }

    public function getTableColumns(string $table_name): array
    {
        $columns = [];
        $schema = $this->connection->getSchemaManager()->createSchema();
        
        // get data
        try {
            $table = $schema->getTable($table_name);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('error to get columns from table: '.$table_name.', '.$e->getMessage(), 404);
        }

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }
        
        return $columns;
    }

    public function getTableData(string $table_name): array
    {
        $data = [];

        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);

        // get data
        try {
            $data = $this->connection->executeQuery('SELECT * FROM '.$table_name)->fetchAll();
        } catch (\Exception $e) {
            $this->errorHelper->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        
        // log to database
        $this->logHelper->log('database-browser', $this->authManager->getUsername().' viewed database table: '.$table_name);

        return $data;
    }

    public function getTableDataByPage(string $table_name, int $page = 1): array
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
            $data = $this->connection->executeQuery($query)->fetchAll();
        } catch (\Exception $e) {
            $this->errorHelper->handleError('error to get data from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }
        
        // log to database
        $this->logHelper->log('database-browser', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
    
        return $data;
    }

    public function countTableData(string $table_name): int 
    {
        $table_data = $this->getTableData($table_name);
        return count($table_data);
    }

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
            $this->errorHelper->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        return $data[0];
    }

    public function updateValue(string $table_name, string $row, string $value, int $id): void
    {
        // query builder
        $query = "UPDATE $table_name SET $row = :value WHERE id = :id";

        try {
            $this->connection->executeStatement($query, [
                'value' => $value,
                'id' => $id,
            ]);
            $this->logHelper->log('database', $this->authManager->getUsername().': edited '.$row.' -> '.$value.', in table: '.$table_name);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('error to update value: '.$value.' in: '.$table_name.' id: '.$id.', error: '.$e->getMessage(), 500);
        }
    }

    public function deleteRowFromTable(string $table_name, string $id): void
    {
        // log to database
        $this->logHelper->log('database-browser', $this->authManager->getUsername().' deleted row: '.$id.', table: '.$table_name);

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
    }
}   
