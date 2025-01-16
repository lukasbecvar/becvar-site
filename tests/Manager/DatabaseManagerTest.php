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
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;
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
    private EntityManagerInterface & MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->logManager = $this->createMock(LogManager::class);
        $this->connection = $this->createMock(Connection::class);
        $this->authManager = $this->createMock(AuthManager::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // create database manager instance
        $this->databaseManager = new DatabaseManager(
            $this->logManager,
            $this->connection,
            $this->authManager,
            $this->errorManager,
            $this->entityManager
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

    /**
     * Test truncate table
     *
     * @return void
     */
    public function testTableTruncate(): void
    {
        // expect executeStatement call
        $this->connection->expects($this->once())->method('executeStatement')->with(
            $this->stringContains('TRUNCATE TABLE test_db.test_table')
        );

        // expect log manager call
        $this->logManager->expects($this->once())->method('log')->with(
            $this->equalTo('database'),
            $this->stringContains('truncated table: test_table')
        );

        // call tested method
        $this->databaseManager->tableTruncate('test_db', 'test_table');
    }

    /**
     * Test truncate table throws exception
     *
     * @return void
     */
    public function testTableTruncateThrowsException(): void
    {
        // expect executeStatement call
        $this->connection->expects($this->once())->method('executeStatement')
            ->willThrowException(new Exception('Database error'));

        // expect handleError call
        $this->errorManager->expects($this->once())->method('handleError')->with(
            $this->stringContains('error truncating table: Database error'),
            $this->equalTo(Response::HTTP_INTERNAL_SERVER_ERROR)
        );

        // call tested method
        $this->databaseManager->tableTruncate('test_db', 'test_table');
    }
}
