<?php

namespace App\Manager;

use App\Helper\ErrorHelper;
use Doctrine\DBAL\Connection;

/*
    Database manager provides methods for get/edit database data
*/

class DatabaseManager
{
    private $connection;
    private $errorHelper;
        
    public function __construct(
        Connection $connection,
        ErrorHelper $errorHelper
    ) {
        $this->connection = $connection;
        $this->errorHelper = $errorHelper;
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
        
        return $data;
    }

    public function countTableData(string $table_name): int {
        $table_data = $this->getTableData($table_name);
        return count($table_data);
    }
}
