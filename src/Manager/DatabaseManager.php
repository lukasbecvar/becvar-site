<?php

namespace App\Manager;

use App\Entity\Message;
use App\Entity\Todo;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/*
    Database manager provides methods for get/edit database data
*/

class DatabaseManager
{
    private $logManager;
    private $connection;
    private $authManager;
    private $errorManager;
    private $entityManager;
        
    public function __construct(
        LogManager $logManager,
        Connection $connection,
        AuthManager $authManager,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->connection = $connection;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    public function getTables(): ?array
    {
        $tables_list = [];

        try {
            $platform = $this->connection->getDatabasePlatform();
            $sql = $platform->getListTablesSQL();
            $tables = $this->connection->executeQuery($sql)->fetchAll();   
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
            $this->errorManager->handleError('error to get columns from table: '.$table_name.', '.$e->getMessage(), 404);
        }

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }
        
        return $columns;
    }

    public function getTableData(string $table_name, bool $log = true): array
    {
        $data = [];

        // escape name from sql query
        $table_name = $this->connection->quoteIdentifier($table_name);

        // get data
        try {
            $data = $this->connection->executeQuery('SELECT * FROM '.$table_name)->fetchAll();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        
        // log to database
        if ($log) {
            $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
        }

        return $data;
    }

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
            $data = $this->connection->executeQuery($query)->fetchAll();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get data from table: ' . $table_name . ', ' . $e->getMessage(), 404);
        }
        
        // log to database
        if ($log) {
            $this->logManager->log('database', $this->authManager->getUsername() . ' viewed database table: ' . $table_name);
        }
    
        return $data;
    }

    public function countTableData(string $table_name): int 
    {
        $table_data = $this->getTableData($table_name, false);
        return count($table_data);
    }

    public function countTableDataByPage(string $table_name, int $page): int 
    {
        $table_data = $this->getTableDataByPage($table_name, $page, false);
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
            $this->errorManager->handleError('error to get data from table: '.$table_name.', '.$e->getMessage(), 404);
        }
        return $data[0];
    }

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

    public function getMessages(string $status, int $page)
    {
        $limit = $_ENV['ITEMS_PER_PAGE'];

        // calculate offset
        $offset = ($page - 1) * $limit;
    
        // get messages repository
        $repository = $this->entityManager->getRepository(Message::class);
    
        try {
            return $repository->findBy(['status' => $status], null, $limit, $offset);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get messages: ' . $e->getMessage(), 500);
            return null;
        }
    }

    public function closeMessage(int $id): void 
    {
        // get message repository
        $message = $this->entityManager->getRepository(Message::class)->find($id);

        // check if message found
        if ($message !== null) {
            // close message
            $message->setStatus('closed');
            // update message
            $this->entityManager->flush();
        }
    }

    public function closeTodo(int $id): void
    {
        // get current date
        $date = date('d.m.Y H:i:s');

        // get todo repository
        $todo = $this->entityManager->getRepository(Todo::class)->find($id);

        // check if todo found
        if ($todo !== null) {
            // close todo
            $todo->setStatus('completed');

            // update closed time
            $todo->setCompletedTime($date);

            // update todo
            $this->entityManager->flush();
        }  
    }

    public function getMessageCountByIpAddress(string $ip_address): int
    {
        // build query
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(m.id) FROM App\Entity\Message m WHERE m.ip_address = :ip_address AND m.status = :status'
        );

        // set query parameter
        $query->setParameter('status', 'open');
        $query->setParameter('ip_address', $ip_address);
    
        // execute query
        try {
            return $query->getSingleScalarResult();
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get messages count: '.$e->getMessage(), 500);
            return 0;
        }
    }

    public function getTodos(string $status): ?array
    {
        $repository = $this->entityManager->getRepository(Todo::class);

        // check if repository found
        if ($repository !== null) {
            try {
                $todos = $repository->findBy(['status' => $status]);
                return array_reverse($todos);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to get todos: '.$e->getMessage(), 500);
            }
        } else {
            return [];
        }
    }

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
}   
