<?php

namespace Tests\Middleware;

use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use App\Middleware\DatabaseOnlineMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DatabaseOnlineMiddlewareTest
 *
 * Test cases for DatabaseOnlineMiddleware class
 *
 * @package App\Tests\Middleware
 */
class DatabaseOnlineMiddlewareTest extends TestCase
{
    /** tested middleware */
    private DatabaseOnlineMiddleware $middleware;

    private ErrorManager & MockObject $errorManagerMock;
    private Connection & MockObject $doctrineConnectionMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->doctrineConnectionMock = $this->createMock(Connection::class);

        // create instance of DatabaseOnlineMiddleware
        $this->middleware = new DatabaseOnlineMiddleware(
            $this->errorManagerMock,
            $this->doctrineConnectionMock
        );
    }

    /**
     * Test database connection succeeds
     *
     * @return void
     */
    public function testDatabaseConnectionSucceeds(): void
    {
        // mock successful database connection
        $this->doctrineConnectionMock->expects($this->once())->method('executeQuery')->with('SELECT 1');

        // expect no error handling called
        $this->errorManagerMock->expects($this->never())->method('handleError');

        // execute method
        $this->middleware->onKernelRequest();
    }

    /**
     * Test database connection fails
     *
     * @return void
     */
    public function testDatabaseConnectionFails(): void
    {
        // mock database connection failure
        $exceptionMessage = 'Connection refused';
        $this->doctrineConnectionMock->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1')
            ->willThrowException(new \Exception($exceptionMessage));

        // expect error handling called with HTTP_INTERNAL_SERVER_ERROR status
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')
            ->with(
                'database connection error: ' . $exceptionMessage,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

        // execute method
        $this->middleware->onKernelRequest();
    }
}
