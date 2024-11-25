<?php

namespace App\Tests\Manager;

use Exception;
use Doctrine\DBAL\Result;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Schema\Column;
use App\Manager\DatabaseManager;
use Doctrine\DBAL\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

/**
 * Class DatabaseManagerTest
 *
 * Test cases for database manager component
 *
 * @package App\Tests\Manager
 */
class DatabaseManagerTest extends TestCase
{
    private DatabaseManager $databaseManager;
    private LogManager & MockObject $logManager;
    private Connection & MockObject $connection;
    private AuthManager & MockObject $authManager;
    private ErrorManager & MockObject $errorManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->logManager = $this->createMock(LogManager::class);
        $this->connection = $this->createMock(Connection::class);
        $this->authManager = $this->createMock(AuthManager::class);
        $this->errorManager = $this->createMock(ErrorManager::class);

        // create database manager instance
        $this->databaseManager = new DatabaseManager(
            $this->logManager,
            $this->connection,
            $this->authManager,
            $this->errorManager
        );
    }

    /**
     * Test get table culumns list
     *
     * @return void
     */
    public function testGetTableColumns(): void
    {
        $schemaMock = $this->createMock(Schema::class);
        $tableMock = $this->createMock(Table::class);
        $columnMock = $this->createMock(Column::class);
        $columnMock->method('getName')->willReturn('id');
        $tableMock->method('getColumns')->willReturn([$columnMock]);
        $schemaMock->method('getTable')->willReturn($tableMock);

        // mock schema manager
        $schemaManagerMock = $this->createMock(AbstractSchemaManager::class);
        $schemaManagerMock->method('introspectSchema')->willReturn($schemaMock);

        // mock connection
        $this->connection->method('createSchemaManager')->willReturn($schemaManagerMock);

        // call tested method
        $result = $this->databaseManager->getTableColumns('users');

        // assert result
        $this->assertIsArray($result);
        $this->assertEquals(['id'], $result);
    }

    /**
     * Test get table data success
     *
     * @return void
     */
    public function testGetTableDataSuccess(): void
    {
        // mock fetch result
        $resultMock = $this->createMock(Result::class);
        $resultMock->method('fetchAllAssociative')->willReturn([
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ]);

        // mock execute query result
        $this->connection->method('executeQuery')->willReturn($resultMock);

        // expect log call
        $this->authManager->method('getUsername')->willReturn('testUser');
        $this->logManager->expects($this->once())->method('log');

        // call tested method
        $result = $this->databaseManager->getTableData('users');

        // assert result
        $this->assertEquals([
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ], $result);
    }

    /**
     * Test get table data error
     *
     * @return void
     */
    public function testGetTableDataError(): void
    {
        // mock for simulate exception
        $this->connection->method('executeQuery')->will($this->throwException(new Exception('Simulated error')));

        // expect call error manager
        $this->errorManager->expects($this->once())->method('handleError');

        // call tested method
        $result = $this->databaseManager->getTableData('users');

        // assert result
        $this->assertEquals([], $result);
    }
}
