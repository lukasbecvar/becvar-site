<?php

namespace App\Tests\Middleware;

use Doctrine\DBAL\Connection;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use App\Middleware\DatabaseOnlineMiddleware;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class DatabaseOnlineMiddlewareTest
 *
 * Test the DatabaseOnlineMiddleware class
 *
 * @package App\Tests\Middleware
 */
class DatabaseOnlineMiddlewareTest extends TestCase
{
    private MockObject|ErrorManager $errorManagerMock;
    private MockObject|DatabaseOnlineMiddleware $middleware;
    private MockObject|Connection $doctrineConnectionMock;

    protected function setUp(): void
    {
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->doctrineConnectionMock = $this->createMock(Connection::class);

        $this->middleware = new DatabaseOnlineMiddleware(
            $this->errorManagerMock,
            $this->doctrineConnectionMock
        );
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWhenDatabaseIsOnline(): void
    {
        // mocking the executeQuery method to return a value
        $this->doctrineConnectionMock->expects($this->once())->method('executeQuery')->with('SELECT 1');

        // mocking the handleError method to be called once
        $this->middleware->onKernelRequest();
    }

    /**
     * Test the onKernelRequest method
     *
     * @return void
     */
    public function testOnKernelRequestWhenDatabaseIsOffline(): void
    {
        // mocking the executeQuery method to throw an exception
        $exceptionMessage = 'Database connection error';

        // mocking the handleError method to be called once
        $this->doctrineConnectionMock->expects($this->once())->method('executeQuery')->willThrowException(new \Exception($exceptionMessage));

        // mocking the handleError method to be called once
        $this->errorManagerMock->expects($this->once())->method('handleError')->with('database connection error: ' . $exceptionMessage, 500);

        // calling the onKernelRequest method
        $this->middleware->onKernelRequest();
    }
}
