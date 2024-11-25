<?php

namespace Tests\Middleware;

use Exception;
use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use App\Middleware\DatabaseOnlineMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DatabaseOnlineMiddlewareTest
 *
 * Test for database online middleware
 *
 * @package App\Tests\Middleware
 */
class DatabaseOnlineMiddlewareTest extends TestCase
{
    private DatabaseOnlineMiddleware $middleware;
    private ErrorManager & MockObject $errorManagerMock;
    private Connection & MockObject $doctrineConnectionMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->doctrineConnectionMock = $this->createMock(Connection::class);

        // create database online middleware instance
        $this->middleware = new DatabaseOnlineMiddleware(
            $this->errorManagerMock,
            $this->doctrineConnectionMock
        );
    }

    /**
     * Test database connection success
     *
     * @return void
     */
    public function testDatabaseConnectionSuccess(): void
    {
        // mock successful database connection
        $this->doctrineConnectionMock->expects($this->once())->method('executeQuery')->with('SELECT 1');

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // call middleware
        $this->middleware->onKernelRequest();
    }

    /**
     * Test database connection fail
     *
     * @return void
     */
    public function testDatabaseConnectionFail(): void
    {
        // mock database connection failure
        $exceptionMessage = 'Connection refused';
        $this->doctrineConnectionMock->expects($this->once())
            ->method('executeQuery')->with('SELECT 1')->willThrowException(new Exception($exceptionMessage));

        // expect error handling called with HTTP_INTERNAL_SERVER_ERROR status
        $this->errorManagerMock->expects($this->once())->method('handleError')->with(
            'database connection error: ' . $exceptionMessage,
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        // call middleware
        $this->middleware->onKernelRequest();
    }
}
