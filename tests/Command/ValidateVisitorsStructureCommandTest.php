<?php

namespace App\Tests\Command;

use Exception;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use App\Command\ValidateVisitorsStructureCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ValidateVisitorsStructureCommandTest
 *
 * Test cases for validate visitors structure command
 *
 * @package App\Tests\Command
 */
class ValidateVisitorsStructureCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private ValidateVisitorsStructureCommand $command;
    private Connection & MockObject $doctrineConnectionMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->doctrineConnectionMock = $this->createMock(Connection::class);

        // init command instance
        $this->command = new ValidateVisitorsStructureCommand($this->doctrineConnectionMock);
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test execute validate visitors structure command with no duplicates response
     *
     * @return void
     */
    public function testExecuteValidateStructutreCommandWithNoDuplicates(): void
    {
        // mock fetchOne to simulate no duplicates
        $this->doctrineConnectionMock->method('fetchOne')->willReturnOnConsecutiveCalls(0);

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert command output
        $this->assertStringContainsString('No validation or reorganization needed', $this->commandTester->getDisplay());
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute validate visitors structure command with duplicates response
     *
     * @return void
     */
    public function testExecuteValidateStructutreCommandWithDuplicates(): void
    {
        // mock fetchOne to simulate duplicates and max id
        $this->doctrineConnectionMock->method('fetchOne')->willReturnOnConsecutiveCalls(5, 10);

        // expect executeQuery calls
        $this->doctrineConnectionMock->expects($this->exactly(4))->method('executeQuery');

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert command output
        $this->assertStringContainsString('5 duplicate record(s) have been deleted', $this->commandTester->getDisplay());
        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * Test execute validate visitors structure command with exception response
     *
     * @return void
     */
    public function testExecuteValidateStructutreCommandWithException(): void
    {
        // mock fetchOne to throw an exception
        $this->doctrineConnectionMock->method('fetchOne')->willThrowException(new Exception('Database error'));

        // execute command
        $exitCode = $this->commandTester->execute([]);

        // assert command output
        $this->assertStringContainsString('Process error: Database error', $this->commandTester->getDisplay());
        $this->assertSame(Command::FAILURE, $exitCode);
    }
}
